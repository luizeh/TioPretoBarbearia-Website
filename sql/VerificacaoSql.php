<?php

include_once __DIR__ . '/../config/connection.php';

/**
 * VerificacaoSql — códigos de confirmação de e-mail.
 *
 * Regras:
 *  - Código numérico de 6 dígitos, aleatório.
 *  - Armazenado como hash (nunca em texto puro).
 *  - Expira após VALIDADE_MIN minutos.
 *  - Invalidado após o uso (usado = 1).
 *  - Limite de tentativas incorretas (MAX_TENTATIVAS).
 *  - Substituído a cada novo envio (os anteriores viram usado = 1).
 *  - Reenvio com tempo mínimo (COOLDOWN_SEG) e limite por hora (MAX_REENVIOS_HORA).
 */
class VerificacaoSql
{
    const VALIDADE_MIN      = 10;  // minutos de validade do código
    const MAX_TENTATIVAS    = 5;   // tentativas incorretas antes de invalidar
    const COOLDOWN_SEG      = 60;  // tempo mínimo entre reenvios (segundos)
    const MAX_REENVIOS_HORA = 5;   // limite de códigos gerados por hora

    /**
     * Gera um novo código para o usuário, invalidando os anteriores.
     * Retorna o código em texto puro (para envio por e-mail — nunca é salvo assim).
     */
    public static function gerarParaUsuario(int $usuarioId, string $email): string
    {
        $pdo = Connection::getConnection();

        // Invalida códigos ativos anteriores.
        $pdo->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE usuario_id = :u AND usado = 0')
            ->execute([':u' => $usuarioId]);

        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash   = password_hash($codigo, PASSWORD_DEFAULT);

        // VALIDADE_MIN é uma constante inteira — seguro interpolar no INTERVAL.
        $stmt = $pdo->prepare(
            'INSERT INTO codigos_verificacao (usuario_id, email, codigo_hash, expira_em)
             VALUES (:u, :e, :h, DATE_ADD(NOW(), INTERVAL ' . self::VALIDADE_MIN . ' MINUTE))'
        );
        $stmt->execute([':u' => $usuarioId, ':e' => $email, ':h' => $hash]);

        return $codigo;
    }

    /**
     * Verifica o código informado para o usuário.
     * Retorna ['success' => bool, 'message' => string].
     * Em caso de sucesso, marca a conta como verificada e o código como usado.
     */
    public static function verificar(int $usuarioId, string $codigo): array
    {
        $pdo = Connection::getConnection();
        $codigo = preg_replace('/\D/', '', (string) $codigo);

        if (!preg_match('/^\d{6}$/', $codigo)) {
            return ['success' => false, 'message' => 'Informe o código de 6 dígitos.'];
        }

        $stmt = $pdo->prepare(
            'SELECT id, codigo_hash, tentativas, usado, (NOW() > expira_em) AS expirado
             FROM codigos_verificacao
             WHERE usuario_id = :u AND usado = 0
             ORDER BY created_at DESC, id DESC
             LIMIT 1'
        );
        $stmt->execute([':u' => $usuarioId]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            return ['success' => false, 'message' => 'Nenhum código ativo. Solicite um novo código.'];
        }

        if ((int) $registro['expirado'] === 1) {
            self::invalidar((int) $registro['id']);
            return ['success' => false, 'message' => 'Código expirado. Solicite um novo código.'];
        }

        if ((int) $registro['tentativas'] >= self::MAX_TENTATIVAS) {
            self::invalidar((int) $registro['id']);
            return ['success' => false, 'message' => 'Muitas tentativas. Solicite um novo código.'];
        }

        if (password_verify($codigo, $registro['codigo_hash'])) {
            $pdo->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE id = :id')
                ->execute([':id' => (int) $registro['id']]);
            $pdo->prepare('UPDATE usuarios SET email_verificado = 1, email_verificado_em = NOW() WHERE id = :u')
                ->execute([':u' => $usuarioId]);
            return ['success' => true, 'message' => 'E-mail verificado com sucesso!'];
        }

        // Código incorreto: incrementa tentativas.
        $novasTentativas = (int) $registro['tentativas'] + 1;
        $pdo->prepare('UPDATE codigos_verificacao SET tentativas = :t WHERE id = :id')
            ->execute([':t' => $novasTentativas, ':id' => (int) $registro['id']]);

        if ($novasTentativas >= self::MAX_TENTATIVAS) {
            self::invalidar((int) $registro['id']);
            return ['success' => false, 'message' => 'Código incorreto. Limite de tentativas atingido — solicite um novo código.'];
        }

        $restantes = self::MAX_TENTATIVAS - $novasTentativas;
        return ['success' => false, 'message' => "Código incorreto. Tentativas restantes: {$restantes}."];
    }

    private static function invalidar(int $id): void
    {
        Connection::getConnection()
            ->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE id = :id')
            ->execute([':id' => $id]);
    }

    /**
     * Avalia se o usuário pode solicitar um novo código agora.
     * Retorna ['pode' => bool, 'espera' => int(segundos), 'message' => string].
     */
    public static function statusReenvio(int $usuarioId): array
    {
        $pdo = Connection::getConnection();

        // Limite por hora.
        $qtd = $pdo->prepare('SELECT COUNT(*) FROM codigos_verificacao WHERE usuario_id = :u AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $qtd->execute([':u' => $usuarioId]);
        if ((int) $qtd->fetchColumn() >= self::MAX_REENVIOS_HORA) {
            return ['pode' => false, 'espera' => 0, 'message' => 'Você atingiu o limite de envios. Tente novamente mais tarde.'];
        }

        // Cooldown desde o último envio.
        $ultimo = $pdo->prepare('SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) AS decorrido FROM codigos_verificacao WHERE usuario_id = :u ORDER BY created_at DESC, id DESC LIMIT 1');
        $ultimo->execute([':u' => $usuarioId]);
        $linha = $ultimo->fetch(PDO::FETCH_ASSOC);

        if ($linha !== false) {
            $decorrido = (int) $linha['decorrido'];
            if ($decorrido < self::COOLDOWN_SEG) {
                $espera = self::COOLDOWN_SEG - $decorrido;
                return ['pode' => false, 'espera' => $espera, 'message' => "Aguarde {$espera}s para reenviar o código."];
            }
        }

        return ['pode' => true, 'espera' => 0, 'message' => ''];
    }
}

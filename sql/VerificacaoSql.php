<?php

include_once __DIR__ . '/../config/Connection.php';

/**
 * VerificacaoSql — códigos de verificação/recuperação reutilizáveis.
 *
 * A mesma estrutura atende três propósitos (coluna `proposito`):
 *   - VerificacaoSql::EMAIL       → confirmação de e-mail
 *   - VerificacaoSql::TELEFONE    → confirmação de telefone (WhatsApp)
 *   - VerificacaoSql::RECUPERACAO → recuperação de senha
 *
 * Regras (iguais para todos os propósitos):
 *  - Código numérico de 6 dígitos, aleatório (random_int).
 *  - Armazenado como hash (password_hash) — nunca em texto puro.
 *  - Expira após VALIDADE_MIN minutos.
 *  - Invalidado após o uso (usado = 1).
 *  - Limite de tentativas incorretas (MAX_TENTATIVAS).
 *  - Cada novo envio invalida os anteriores do mesmo propósito.
 *  - Reenvio com tempo mínimo (COOLDOWN_SEG) e teto por hora (MAX_REENVIOS_HORA).
 *
 * Esta classe cuida apenas do ciclo de vida do código. Aplicar o efeito no
 * usuário (marcar verificado, trocar e-mail/telefone, redefinir senha) é
 * responsabilidade de quem chama — mantendo a estrutura genérica e reutilizável.
 */
class VerificacaoSql
{
    const EMAIL       = 'email_verificacao';
    const TELEFONE    = 'telefone_verificacao';
    const RECUPERACAO = 'recuperacao';

    const VALIDADE_MIN      = 10;  // minutos de validade do código
    const MAX_TENTATIVAS    = 5;   // tentativas incorretas antes de invalidar
    const COOLDOWN_SEG      = 60;  // tempo mínimo entre reenvios (segundos)
    const MAX_REENVIOS_HORA = 5;   // limite de códigos gerados por hora/propósito

    private static function propositoValido(string $proposito): bool
    {
        return in_array($proposito, [self::EMAIL, self::TELEFONE, self::RECUPERACAO], true);
    }

    /**
     * Gera um novo código para o usuário/propósito, invalidando os anteriores.
     * Retorna o código em texto puro (para envio — nunca é salvo assim).
     *
     * @param string $canal   'email' | 'whatsapp'
     * @param string $destino e-mail ou telefone (só dígitos) de destino
     */
    public static function gerar(int $usuarioId, string $proposito, string $canal, string $destino): string
    {
        if (!self::propositoValido($proposito)) {
            throw new InvalidArgumentException('Propósito de verificação inválido.');
        }

        $pdo = Connection::getConnection();

        // Invalida códigos ativos anteriores do mesmo propósito.
        $pdo->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE usuario_id = :u AND proposito = :p AND usado = 0')
            ->execute([':u' => $usuarioId, ':p' => $proposito]);

        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash   = password_hash($codigo, PASSWORD_DEFAULT);

        // VALIDADE_MIN é uma constante inteira — seguro interpolar no INTERVAL.
        $stmt = $pdo->prepare(
            'INSERT INTO codigos_verificacao (usuario_id, proposito, canal, destino, email, codigo_hash, expira_em)
             VALUES (:u, :p, :c, :d, :e, :h, DATE_ADD(NOW(), INTERVAL ' . self::VALIDADE_MIN . ' MINUTE))'
        );
        $stmt->execute([
            ':u' => $usuarioId,
            ':p' => $proposito,
            ':c' => $canal,
            ':d' => $destino,
            // Mantém a coluna legada preenchida quando o destino é um e-mail.
            ':e' => $canal === 'email' ? $destino : null,
            ':h' => $hash,
        ]);

        return $codigo;
    }

    /**
     * Valida o código informado para o usuário/propósito.
     * Retorna ['success' => bool, 'message' => string].
     * Em caso de sucesso, marca o código como usado (o efeito na conta é do chamador).
     */
    public static function validar(int $usuarioId, string $proposito, string $codigo): array
    {
        if (!self::propositoValido($proposito)) {
            return ['success' => false, 'message' => 'Propósito de verificação inválido.'];
        }

        $pdo    = Connection::getConnection();
        $codigo = preg_replace('/\D/', '', (string) $codigo);

        if (!preg_match('/^\d{6}$/', $codigo)) {
            return ['success' => false, 'message' => 'Informe o código de 6 dígitos.'];
        }

        $stmt = $pdo->prepare(
            'SELECT id, codigo_hash, tentativas, (NOW() > expira_em) AS expirado
             FROM codigos_verificacao
             WHERE usuario_id = :u AND proposito = :p AND usado = 0
             ORDER BY created_at DESC, id DESC
             LIMIT 1'
        );
        $stmt->execute([':u' => $usuarioId, ':p' => $proposito]);
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
            self::invalidar((int) $registro['id']);
            return ['success' => true, 'message' => 'Código confirmado.'];
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

    /**
     * Invalida todos os códigos ativos de um propósito (ex.: após redefinir senha).
     */
    public static function invalidarProposito(int $usuarioId, string $proposito): void
    {
        Connection::getConnection()
            ->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE usuario_id = :u AND proposito = :p AND usado = 0')
            ->execute([':u' => $usuarioId, ':p' => $proposito]);
    }

    private static function invalidar(int $id): void
    {
        Connection::getConnection()
            ->prepare('UPDATE codigos_verificacao SET usado = 1 WHERE id = :id')
            ->execute([':id' => $id]);
    }

    /**
     * Avalia se o usuário pode solicitar um novo código agora (por propósito).
     * Retorna ['pode' => bool, 'espera' => int(segundos), 'message' => string].
     */
    public static function statusReenvio(int $usuarioId, string $proposito): array
    {
        $pdo = Connection::getConnection();

        // Limite por hora.
        $qtd = $pdo->prepare('SELECT COUNT(*) FROM codigos_verificacao WHERE usuario_id = :u AND proposito = :p AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $qtd->execute([':u' => $usuarioId, ':p' => $proposito]);
        if ((int) $qtd->fetchColumn() >= self::MAX_REENVIOS_HORA) {
            return ['pode' => false, 'espera' => 0, 'message' => 'Você atingiu o limite de envios. Tente novamente mais tarde.'];
        }

        // Cooldown desde o último envio.
        $ultimo = $pdo->prepare('SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) AS decorrido FROM codigos_verificacao WHERE usuario_id = :u AND proposito = :p ORDER BY created_at DESC, id DESC LIMIT 1');
        $ultimo->execute([':u' => $usuarioId, ':p' => $proposito]);
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

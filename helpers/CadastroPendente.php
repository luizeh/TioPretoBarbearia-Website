<?php

/**
 * CadastroPendente.php
 * Armazena um cadastro AINDA NÃO confirmado inteiramente na SESSÃO do servidor —
 * o cliente só é inserido na tabela `usuarios` depois de confirmar e-mail E
 * telefone. Assim, cadastros abandonados não deixam registros no banco nem
 * ocupam e-mail/telefone (constraints únicas).
 *
 * Os códigos seguem as mesmas regras do VerificacaoSql (hash, expiração,
 * tentativas, cooldown e teto por hora), mas vivem apenas na sessão.
 * A senha é guardada já como hash (nunca em texto puro).
 *
 * Requer sessão ativa (helpers::iniciarSessao()) antes do uso.
 */
class CadastroPendente
{
    const CHAVE = 'cadastro_pendente';

    const EMAIL    = 'email';
    const TELEFONE = 'telefone';

    const VALIDADE_MIN      = 10;  // minutos de validade do código
    const MAX_TENTATIVAS    = 5;   // tentativas incorretas antes de invalidar
    const COOLDOWN_SEG      = 60;  // tempo mínimo entre reenvios (segundos)
    const MAX_REENVIOS_HORA = 5;   // limite de códigos gerados por hora/canal

    private static function canalValido(string $canal): bool
    {
        return $canal === self::EMAIL || $canal === self::TELEFONE;
    }

    private static function slotVazio(): array
    {
        return ['verificado' => false, 'hash' => null, 'expira' => 0, 'tentativas' => 0, 'envios' => []];
    }

    /**
     * Inicia um cadastro pendente. $dados já validados; a senha vem em texto e
     * é convertida para hash aqui. Sobrescreve qualquer pendência anterior.
     */
    public static function iniciar(array $dados): void
    {
        $_SESSION[self::CHAVE] = [
            'dados' => [
                'nome'       => $dados['nome'],
                'sobrenome'  => $dados['sobrenome'],
                'email'      => $dados['email'],
                'telefone'   => $dados['telefone'],
                'cidade'     => $dados['cidade'],
                'senha_hash' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            ],
            'codigos' => [
                self::EMAIL    => self::slotVazio(),
                self::TELEFONE => self::slotVazio(),
            ],
            'criado' => time(),
        ];
    }

    public static function existe(): bool
    {
        return !empty($_SESSION[self::CHAVE]['dados']);
    }

    /** Dados do cadastro (inclui senha_hash) para persistir ao final. */
    public static function dados(): ?array
    {
        return $_SESSION[self::CHAVE]['dados'] ?? null;
    }

    public static function limpar(): void
    {
        unset($_SESSION[self::CHAVE]);
    }

    public static function verificado(string $canal): bool
    {
        return !empty($_SESSION[self::CHAVE]['codigos'][$canal]['verificado']);
    }

    /** Ambos os canais confirmados? */
    public static function completo(): bool
    {
        return self::verificado(self::EMAIL) && self::verificado(self::TELEFONE);
    }

    /**
     * Gera um novo código de 6 dígitos para o canal, invalidando o anterior.
     * Retorna o código em texto puro (para envio — só o hash fica salvo).
     */
    public static function gerarCodigo(string $canal): string
    {
        if (!self::canalValido($canal) || !self::existe()) {
            throw new RuntimeException('Cadastro pendente inválido.');
        }

        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $slot = $_SESSION[self::CHAVE]['codigos'][$canal];
        $slot['hash']       = password_hash($codigo, PASSWORD_DEFAULT);
        $slot['expira']     = time() + self::VALIDADE_MIN * 60;
        $slot['tentativas'] = 0;
        $slot['envios'][]   = time();
        $_SESSION[self::CHAVE]['codigos'][$canal] = $slot;

        return $codigo;
    }

    /**
     * Valida o código informado para o canal. Retorna ['success','message'].
     * Em caso de sucesso marca o canal como verificado.
     */
    public static function validar(string $canal, string $codigo): array
    {
        if (!self::canalValido($canal) || !self::existe()) {
            return ['success' => false, 'message' => 'Sessão de cadastro expirada. Refaça o cadastro.'];
        }

        $codigo = preg_replace('/\D/', '', (string) $codigo);
        if (!preg_match('/^\d{6}$/', $codigo)) {
            return ['success' => false, 'message' => 'Informe o código de 6 dígitos.'];
        }

        $slot = $_SESSION[self::CHAVE]['codigos'][$canal];

        if (empty($slot['hash'])) {
            return ['success' => false, 'message' => 'Nenhum código ativo. Solicite um novo código.'];
        }
        if (time() > (int) $slot['expira']) {
            $slot['hash'] = null;
            $_SESSION[self::CHAVE]['codigos'][$canal] = $slot;
            return ['success' => false, 'message' => 'Código expirado. Solicite um novo código.'];
        }
        if ((int) $slot['tentativas'] >= self::MAX_TENTATIVAS) {
            $slot['hash'] = null;
            $_SESSION[self::CHAVE]['codigos'][$canal] = $slot;
            return ['success' => false, 'message' => 'Muitas tentativas. Solicite um novo código.'];
        }

        if (password_verify($codigo, $slot['hash'])) {
            $slot['verificado'] = true;
            $slot['hash']       = null;
            $_SESSION[self::CHAVE]['codigos'][$canal] = $slot;
            return ['success' => true, 'message' => 'Código confirmado.'];
        }

        $slot['tentativas'] = (int) $slot['tentativas'] + 1;
        $_SESSION[self::CHAVE]['codigos'][$canal] = $slot;

        if ($slot['tentativas'] >= self::MAX_TENTATIVAS) {
            return ['success' => false, 'message' => 'Código incorreto. Limite de tentativas atingido — solicite um novo código.'];
        }
        $restantes = self::MAX_TENTATIVAS - $slot['tentativas'];
        return ['success' => false, 'message' => "Código incorreto. Tentativas restantes: {$restantes}."];
    }

    /**
     * Avalia se pode reenviar agora. Retorna ['pode','espera','message'].
     */
    public static function statusReenvio(string $canal): array
    {
        if (!self::canalValido($canal) || !self::existe()) {
            return ['pode' => false, 'espera' => 0, 'message' => 'Sessão de cadastro expirada.'];
        }

        $envios = $_SESSION[self::CHAVE]['codigos'][$canal]['envios'] ?? [];
        $agora  = time();

        $ultimaHora = array_filter($envios, static fn($t) => $t > $agora - 3600);
        if (count($ultimaHora) >= self::MAX_REENVIOS_HORA) {
            return ['pode' => false, 'espera' => 0, 'message' => 'Você atingiu o limite de envios. Tente novamente mais tarde.'];
        }

        if (!empty($envios)) {
            $decorrido = $agora - (int) end($envios);
            if ($decorrido < self::COOLDOWN_SEG) {
                $espera = self::COOLDOWN_SEG - $decorrido;
                return ['pode' => false, 'espera' => $espera, 'message' => "Aguarde {$espera}s para reenviar o código."];
            }
        }

        return ['pode' => true, 'espera' => 0, 'message' => ''];
    }
}

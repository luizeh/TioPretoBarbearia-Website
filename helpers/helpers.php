<?php
class helpers
{

    static function resposta_json($sucesso, $mensagem, $dados = null, $status = 200)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $sucesso,
            'message' => $mensagem,
            'data'    => $dados
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }


    public static function verificar_login()
    {
        if (empty($_SESSION['usuario_id'])) {
            helpers::resposta_json(false, 'Você precisa estar logado para acessar esta funcionalidade.', null, 401);
        }
    }


    public static function verificar_admin()
    {
        helpers::verificar_login();

        if (empty($_SESSION['usuario_admin']) || $_SESSION['usuario_admin'] != true) {
            helpers::resposta_json(false, 'Você não tem permissão para realizar esta ação.', null, 403);
        }
    }


    // SESSÃO E CSRF

    /**
     * Inicia a sessão apenas se ainda não houver uma ativa.
     */
    public static function iniciarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Só marca Secure quando a requisição é HTTPS (não quebra o dev em HTTP).
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,      // inacessível via document.cookie (mitiga roubo por XSS)
                'secure'   => $secure,   // só trafega em HTTPS quando disponível
                'samesite' => 'Lax',     // reduz superfície de CSRF
            ]);
            session_start();
        }
    }

    /**
     * Retorna o token CSRF da sessão, criando-o se necessário.
     * Usado nos formulários importantes (login, cadastro, verificação, pedido).
     */
    public static function tokenCsrf(): string
    {
        self::iniciarSessao();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Extrai o token CSRF da requisição (header, $_POST ou corpo JSON).
     */
    public static function csrfDaRequisicao(array $body = []): ?string
    {
        return $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? ($_POST['csrf_token'] ?? ($body['csrf_token'] ?? null));
    }

    /**
     * Valida o token CSRF; responde JSON 419 e encerra se for inválido.
     */
    public static function verificarCsrf(array $body = []): void
    {
        self::iniciarSessao();
        $token = self::csrfDaRequisicao($body);
        if (empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            self::resposta_json(false, 'Sessão expirada ou requisição inválida. Recarregue a página e tente novamente.', null, 403);
        }
    }


    // VALIDAÇÕES

    public static function validarTexto($texto, $nomeCampo, $min = 2, $max = 50)
    {
        $texto = preg_replace('/\s+/', ' ', trim($texto));

        if (strlen($texto) < $min || strlen($texto) > $max) {
            self::resposta_json(false, "O campo {$nomeCampo} deve ter entre {$min} e {$max} caracteres.", null, 400);
        }

        if (!preg_match('/^[\p{L}\s]+$/u', $texto)) {
            self::resposta_json(false, "O campo {$nomeCampo} deve conter apenas letras.", null, 400);
        }

        return $texto;
    }


    public static function validarEmail($email)
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::resposta_json(false, 'E-mail inválido.', null, 400);
        }

        return strtolower($email);
    }

    public static function validarTelefone($telefone)
    {
        $telefone = trim($telefone);

        if (!preg_match('/^\+55\s\(\d{2}\)\s\d{5}-\d{4}$/', $telefone)) {
            self::resposta_json(false, 'Telefone inválido.', null, 400);
        }

        return preg_replace('/\D/', '', $telefone);
    }

    public static function normalizarTelefone($telefone): string
    {
        $telefone = preg_replace('/\D/', '', (string) $telefone);
        if ($telefone === '') return '';
        if (!str_starts_with($telefone, '55')) $telefone = '55' . $telefone;
        if (!preg_match('/^55\d{10,11}$/', $telefone)) {
            self::resposta_json(false, 'Telefone inválido.', null, 400);
        }
        return $telefone;
    }


    public static function validarSenha($senha)
    {
        $senha = trim($senha);

        if (strlen($senha) < 8) {
            self::resposta_json(false, 'A senha deve ter no mínimo 8 caracteres.', null, 400);
        }

        if (preg_match('/\s/', $senha)) {
            self::resposta_json(false, 'A senha não pode conter espaços.', null, 400);
        }

        return $senha;
    }

    /**
     * Campo de texto obrigatório genérico (endereço, número, etc.).
     * Rejeita valores compostos apenas de espaços e aplica limites.
     * Diferente de validarTexto: aceita números e pontuação.
     */
    public static function validarObrigatorio($valor, $nomeCampo, $min = 1, $max = 150)
    {
        $valor = preg_replace('/\s+/', ' ', trim((string) $valor));

        if ($valor === '' || mb_strlen($valor) < $min) {
            self::resposta_json(false, "O campo {$nomeCampo} é obrigatório.", null, 400);
        }

        if (mb_strlen($valor) > $max) {
            self::resposta_json(false, "O campo {$nomeCampo} deve ter no máximo {$max} caracteres.", null, 400);
        }

        return $valor;
    }

    /**
     * Campo de texto opcional: normaliza espaços e limita tamanho.
     * Retorna string vazia se não informado.
     */
    public static function validarOpcional($valor, $nomeCampo, $max = 150)
    {
        $valor = preg_replace('/\s+/', ' ', trim((string) $valor));

        if ($valor === '') {
            return '';
        }

        if (mb_strlen($valor) > $max) {
            self::resposta_json(false, "O campo {$nomeCampo} deve ter no máximo {$max} caracteres.", null, 400);
        }

        return $valor;
    }

    /**
     * CEP: exige 8 dígitos e devolve no formato 00000-000.
     */
    public static function validarCep($cep)
    {
        $cep = preg_replace('/\D/', '', (string) $cep);

        if (!preg_match('/^\d{8}$/', $cep)) {
            self::resposta_json(false, 'CEP inválido. Informe os 8 números (00000-000).', null, 400);
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5);
    }

    /**
     * UF (sigla do estado): valida contra a lista oficial e devolve em maiúsculas.
     */
    public static function validarUf($uf)
    {
        $uf = strtoupper(trim((string) $uf));
        $ufs = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
            'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
            'SP', 'SE', 'TO',
        ];

        if (!in_array($uf, $ufs, true)) {
            self::resposta_json(false, 'Selecione um estado (UF) válido.', null, 400);
        }

        return $uf;
    }
}

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

        if (empty($_SESSION['admin']) || $_SESSION['admin'] !== true) {
            helpers::resposta_json(false, 'Você não tem permissão para realizar esta ação.', null, 403);
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
}

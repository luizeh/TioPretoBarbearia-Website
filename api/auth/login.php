<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../config/connection.php');
include_once(__DIR__ . '/../../helpers/helpers.php');

helpers::iniciarSessao();

// PDO com prepared statement — sem SQL injection
$pdo = Connection::getConnection();

$dados = $_POST;

if (($dados['action'] ?? '') == 'login') {

    helpers::verificarCsrf();

    $email = helpers::validarEmail($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    if (empty($senha) || empty($email)) {
        helpers::resposta_json(false, 'E-mail e senha são obrigatórios.', null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    $usuario = $stmt->fetch();

    // Mensagem genérica para não revelar se o e-mail existe.
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        helpers::resposta_json(false, 'E-mail ou senha inválidos.', null, 401);
    }

    // Conta ainda não verificada: valida credenciais mas não conclui o login.
    if (isset($usuario['email_verificado']) && (int) $usuario['email_verificado'] === 0) {
        $_SESSION['pendente_verificacao'] = [
            'usuario_id' => (int) $usuario['id'],
            'email'      => $usuario['email'],
            'nome'       => $usuario['nome'],
        ];
        helpers::resposta_json(
            false,
            'Sua conta ainda não foi verificada. Confirme seu e-mail para continuar.',
            ['redirect' => 'verificar-email.php', 'nao_verificado' => true],
            403
        );
    }

    // Regenera o ID de sessão após autenticação (previne fixation).
    session_regenerate_id(true);
    $_SESSION['usuario_id']    = $usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome'];
    $_SESSION['usuario_admin'] = $usuario['admin'];
    $redirect = $usuario['admin'] ? 'admin/dashboard.php' : 'user/agendamentos.php';
    helpers::resposta_json(true, 'Login realizado com sucesso.', ['redirect' => $redirect], 200);
}

<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../config/connection.php');
include_once(__DIR__ . '/../../helpers/helpers.php');

// PDO com prepared statement — sem SQL injection
$pdo = Connection::getConnection();

$dados = $_POST;

if ($dados['action'] == 'login') {

    $email = $dados['email'];
    $senha = $dados['senha'];

    if (empty($senha) || empty($email)) {
        helpers::resposta_json(false, 'E-mail e senha são obrigatórios.', null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    $usuario = $stmt->fetch();


    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_start();
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_admin'] = $usuario['admin'];
        $redirect = $usuario['admin'] ? 'admin/dashboard.php' : 'user/agendamentos.php';
        helpers::resposta_json(true, 'Login realizado com sucesso.', ['redirect' => $redirect], 200);
    } else {
        helpers::resposta_json(false, 'E-mail ou senha inválidos.', null, 401);
    }
}

<?php
include_once('../config/connection.php');
include_once('../helpers/helpers.php');

// PDO com prepared statement — sem SQL injection
$pdo = Connection::getConnection();

$dados = $_POST;

if($dados['action'] == 'login'){

    $email = $dados['usuario'];
    $senha = $dados['senha'];

    if(empty($senha) || empty($email)){
        helpers::resposta_json(false, 'E-mail e senha são obrigatórios.', null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND senha = :senha");
    $stmt->execute([':email' => $email, ':senha' => $senha]);
    
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        // login encontrado
        helpers::resposta_json(true, $usuario, null, 400);
    } else {
        print_r("Usuário não encontrado.");
    }

}



?>
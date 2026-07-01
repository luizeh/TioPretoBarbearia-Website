<?php
include_once('../config/Connection.php');
include_once('../helpers/helpers.php');

// PDO com prepared statement — sem SQL injection
$pdo = Connection::getConnection();

// print_r($_POST); die;

$data = $_POST;

if($data['action'] == 'login'){

    $email = $data['usuario'];
    $senha = $data['senha'];

    if(!isset($senha) || !isset($email)){
        helpers::resposta_json(false, 'E-mail e senha são obrigatórios.', null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND senha = :senha");
    $stmt->execute([':email' => $email, ':senha' => $senha]);
    
    $usuario = $stmt->fetch(); // retorna array associativo ou false
    
    if ($usuario) {
        // login encontrado
        helpers::resposta_json(false, $usuario, null, 400);
    } else {
        print_r("Usuário não encontrado.");
    }

}



?>
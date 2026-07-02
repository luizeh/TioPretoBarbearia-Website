<?php
include_once('../config/connection.php');
include_once('../helpers/helpers.php');
include_once('../sql/usuarios.sql.php');

$pdo = Connection::getConnection();

$dados = $_POST;

if ($dados['action'] == 'cadastro') {
    if (!isset($dados['nome'], $dados['sobrenome'], $dados['telefone'], $dados['cidade'], $dados['email'], $dados['senha'], $dados['confirmar_senha'])) {
        helpers::resposta_json(false, 'Dados incompletos na requisição.', null, 400);
    }

    $campos = [
        'nome'            => "O nome é obrigatório",
        'sobrenome'       => "O sobrenome é obrigatório",
        'telefone'        => "O telefone é obrigatório",
        'cidade'          => "A cidade é obrigatória",
        'email'           => "O e-mail é obrigatório",
        'senha'           => "A senha é obrigatória",
        'confirmar_senha' => "Confirmar sua senha é obrigatório",
    ];

    foreach ($campos as $campo => $mensagem) {
        if (empty(trim($dados[$campo]))) {
            helpers::resposta_json(false, $mensagem, null, 400);
        }
    }

    if (trim($dados['senha']) !== trim($dados['confirmar_senha'])) {
        helpers::resposta_json(false, 'As senhas não coincidem.', null, 400);
    }

    cadastrarUsuario($pdo, $dados);
}

<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../helpers/helpers.php');
include_once(__DIR__ . '/../../sql/UsuariosSql.php');
include_once(__DIR__ . '/../../sql/LogsSql.php');

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

    $dados['nome'] = helpers::validarTexto($dados['nome'], 'nome');
    $dados['sobrenome'] = helpers::validarTexto($dados['sobrenome'], 'sobrenome');
    $dados['cidade'] = helpers::validarTexto($dados['cidade'], 'cidade');
    $dados['email'] = helpers::validarEmail($dados['email']);
    $dados['telefone'] = helpers::validarTelefone($dados['telefone']);
    $dados['senha'] = helpers::validarSenha($dados['senha']);



    if (trim($dados['senha']) !== trim($dados['confirmar_senha'])) {
        helpers::resposta_json(false, 'As senhas não coincidem.', null, 400);
    }

    $result = UsuariosSql::cadastrar($dados);

    if ($result['success']) {
        LogsSql::registrar((int) $result['id'], 'conta_criada', 'Conta de cliente criada no sistema.');
        helpers::resposta_json(true, 'Cadastro realizado com sucesso!', ['redirect' => 'login.php'], 201);
    } else {
        helpers::resposta_json(false, $result['message'], null, 400);
    }
}

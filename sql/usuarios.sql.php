<?php

function cadastrarUsuario($pdo, $dados)
{
    $checkemail = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $checkemail->execute([':email' => $dados['email']]);

    if ($checkemail->fetch()) {
        return [
            'success' => false,
            'message' => 'E-mail já cadastrado.'
        ];
    }

    $checkTelefone = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = :telefone");
    $checkTelefone->execute([
        ':telefone' => $dados['telefone']
    ]);

    if ($checkTelefone->fetch()) {
        return [
            'success' => false,
            'message' => 'Telefone já cadastrado.'
        ];
    }

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, sobrenome, telefone, email, senha, cidade) VALUES (:nome, :sobrenome, :telefone, :email, :senha, :cidade)");

    $nome      = $dados['nome'];
    $sobrenome = $dados['sobrenome'];
    $telefone  = $dados['telefone'];
    $email     = $dados['email'];
    $cidade    = $dados['cidade'];
    $senha     = password_hash($dados['senha'], PASSWORD_DEFAULT);

    $stmt->execute([
        ':nome'      => $nome,
        ':sobrenome' => $sobrenome,
        ':telefone'  => $telefone,
        ':email'     => $email,
        ':senha'     => $senha,
        ':cidade'    => $cidade,
    ]);

    return [
        'success' => true,
        'message' => 'Usuário cadastrado com sucesso.'
    ];
}

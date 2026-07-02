<?php

function cadastrarUsuario($pdo, $dados)
{
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, sobrenome, telefone, email, senha, cidade) VALUES (:nome, :sobrenome, :telefone, :email, :senha, :cidade)");

    $nome      = preg_replace('/\s+/', ' ', trim($dados['nome']));
    $sobrenome = preg_replace('/\s+/', ' ', trim($dados['sobrenome']));
    $telefone  = preg_replace('/\s+/', ' ', trim($dados['telefone']));
    $email     = preg_replace('/\s+/', ' ', trim($dados['email']));
    $cidade    = preg_replace('/\s+/', ' ', trim($dados['cidade']));
    $senha     = $dados['senha'];

    $stmt->execute([
        ':nome'      => $nome,
        ':sobrenome' => $sobrenome,
        ':telefone'  => $telefone,
        ':email'     => $email,
        ':senha'     => $senha,
        ':cidade'    => $cidade,
    ]);
}

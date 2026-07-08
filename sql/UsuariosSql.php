<?php

include_once __DIR__ . '/../config/connection.php';

class UsuariosSql
{
    public static function cadastrar(array $dados): array
    {
        $pdo = Connection::getConnection();

        $checkEmail = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $checkEmail->execute([':email' => $dados['email']]);

        if ($checkEmail->fetch()) {
            return ['success' => false, 'message' => 'E-mail já cadastrado.'];
        }

        $checkTelefone = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = :telefone");
        $checkTelefone->execute([':telefone' => $dados['telefone']]);

        if ($checkTelefone->fetch()) {
            return ['success' => false, 'message' => 'Telefone já cadastrado.'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, sobrenome, telefone, email, senha, cidade)
            VALUES (:nome, :sobrenome, :telefone, :email, :senha, :cidade)
        ");

        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':telefone'  => $dados['telefone'],
            ':email'     => $dados['email'],
            ':senha'     => password_hash($dados['senha'], PASSWORD_DEFAULT),
            ':cidade'    => $dados['cidade'],
        ]);

        return ['success' => true, 'message' => 'Usuário cadastrado com sucesso.'];
    }
}

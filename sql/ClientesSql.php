<?php

include_once __DIR__ . '/../config/connection.php';

class ClientesSql
{
    public static function listar(int $limite = 100, int $offset = 0): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, nome, sobrenome, email, telefone, cidade, created_at
            FROM usuarios
            WHERE admin = 0
            ORDER BY nome
            LIMIT :limite OFFSET :offset
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contar(): int
    {
        $pdo = Connection::getConnection();
        return (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE admin = 0")->fetchColumn();
    }

    public static function editar(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nome = :nome, sobrenome = :sobrenome, email = :email,
                telefone = :telefone, cidade = :cidade
            WHERE id = :id
        ");
        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':email'     => $dados['email'],
            ':telefone'  => $dados['telefone'],
            ':cidade'    => $dados['cidade'],
            ':id'        => $id,
        ]);
    }

    public static function excluir(int $id): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id AND admin = 0");
        $stmt->execute([':id' => $id]);
    }
}

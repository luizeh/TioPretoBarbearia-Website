<?php

include_once __DIR__ . '/../config/Connection.php';

class ServicosSql
{
    public static function listar(): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, nome, foto_url, descricao, preco, tempo_estimado
            FROM servicos
            ORDER BY nome
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function criar(array $dados): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO servicos (nome, descricao, preco, tempo_estimado, foto_url)
            VALUES (:nome, :descricao, :preco, :tempo_estimado, :foto_url)
        ");
        $stmt->execute([
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'] ?? null,
            ':preco'          => $dados['preco'],
            ':tempo_estimado' => (int) $dados['tempo_estimado'],
            ':foto_url'       => $dados['foto_url'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function editar(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            UPDATE servicos
            SET nome = :nome, descricao = :descricao, preco = :preco,
                tempo_estimado = :tempo_estimado, foto_url = :foto_url
            WHERE id = :id
        ");
        $stmt->execute([
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'] ?? null,
            ':preco'          => $dados['preco'],
            ':tempo_estimado' => (int) $dados['tempo_estimado'],
            ':foto_url'       => $dados['foto_url'] ?? null,
            ':id'             => $id,
        ]);
    }

    public static function excluir(int $id): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public static function contar(): int
    {
        $pdo = Connection::getConnection();
        return (int) $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
    }
}

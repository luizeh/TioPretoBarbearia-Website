<?php

include_once __DIR__ . '/../config/connection.php';

class LogsSql
{
    public static function registrar(int $usuarioId, string $acao, string $descricao): void
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('INSERT INTO logs (usuario_id, acao, descricao) VALUES (:usuario_id, :acao, :descricao)');
        $stmt->execute([':usuario_id' => $usuarioId, ':acao' => $acao, ':descricao' => $descricao]);
    }

    public static function listar(int $limite = 20): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, usuario_id, acao, descricao, created_at
            FROM logs ORDER BY created_at DESC, id DESC LIMIT :limite");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

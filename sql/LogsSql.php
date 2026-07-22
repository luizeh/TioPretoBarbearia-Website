<?php

include_once __DIR__ . '/../config/Connection.php';

class LogsSql
{
    public static function registrar(int $usuarioId, string $acao, string $descricao): void
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('INSERT INTO logs (usuario_id, acao, descricao) VALUES (:usuario_id, :acao, :descricao)');
        $stmt->execute([':usuario_id' => $usuarioId, ':acao' => $acao, ':descricao' => $descricao]);
    }

    public static function listar(int $limite = 20, int $offset = 0): array
    {
        $pdo = Connection::getConnection();
        // LEFT JOIN para trazer o nome de quem fez a ação; LEFT para o log
        // sobreviver caso o usuário seja removido depois.
        $stmt = $pdo->prepare("SELECT logs.id, logs.usuario_id, logs.acao, logs.descricao, logs.created_at,
                   u.nome AS usuario_nome, u.sobrenome AS usuario_sobrenome
            FROM logs
            LEFT JOIN usuarios u ON u.id = logs.usuario_id
            ORDER BY logs.created_at DESC, logs.id DESC LIMIT :limite OFFSET :offset");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contar(): int
    {
        $pdo = Connection::getConnection();
        return (int) $pdo->query("SELECT COUNT(*) FROM logs")->fetchColumn();
    }
}

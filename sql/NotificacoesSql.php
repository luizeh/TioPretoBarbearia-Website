<?php
include_once __DIR__ . '/../config/Connection.php';

class NotificacoesSql
{
    public static function listarPorUsuario(int $usuarioId, int $limite = 50): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT id, tipo, titulo, mensagem, lida, created_at FROM notificacoes WHERE usuario_id = :usuario_id ORDER BY created_at DESC, id DESC LIMIT :limite');
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarNaoLidas(int $usuarioId): int
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notificacoes WHERE usuario_id = :usuario_id AND lida = 0');
        $stmt->execute([':usuario_id' => $usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    public static function criar(int $usuarioId, string $tipo, string $titulo, string $mensagem): void
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem) VALUES (:usuario_id, :tipo, :titulo, :mensagem)');
        $stmt->execute([':usuario_id' => $usuarioId, ':tipo' => $tipo, ':titulo' => $titulo, ':mensagem' => $mensagem]);
    }

    public static function marcarComoLida(int $id, int $usuarioId): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('UPDATE notificacoes SET lida = 1 WHERE id = :id AND usuario_id = :usuario_id')->execute([':id' => $id, ':usuario_id' => $usuarioId]);
    }

    public static function marcarTodasComoLidas(int $usuarioId): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('UPDATE notificacoes SET lida = 1 WHERE usuario_id = :usuario_id AND lida = 0')->execute([':usuario_id' => $usuarioId]);
    }
}

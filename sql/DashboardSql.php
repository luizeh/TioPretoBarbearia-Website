<?php

include_once __DIR__ . '/../config/connection.php';

class DashboardSql
{
    public static function buscarUsuario(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                sobrenome,
                email,
                telefone,
                cidade
            FROM usuarios
            WHERE id = :id
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function contarUsuarios(): int
    {
        $pdo = Connection::getConnection();

        return (int) $pdo
            ->query("SELECT COUNT(*) FROM usuarios")
            ->fetchColumn();
    }

    public static function listarUsuarios(int $limite, int $offset): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                sobrenome,
                email,
                telefone,
                cidade
            FROM usuarios
            ORDER BY nome
            LIMIT :limite OFFSET :offset
        ");

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

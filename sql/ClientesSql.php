<?php

include_once __DIR__ . '/../config/Connection.php';
include_once __DIR__ . '/UsuariosSql.php';

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

    public static function criar(array $dados): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, sobrenome, email, telefone, cidade, senha, admin)
            VALUES (:nome, :sobrenome, :email, :telefone, :cidade, :senha, 0)
        ");
        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':email'     => $dados['email'],
            ':telefone'  => $dados['telefone'],
            ':cidade'    => $dados['cidade'],
            ':senha'     => password_hash($dados['senha'] ?? '12345678', PASSWORD_DEFAULT),
        ]);
        return (int) $pdo->lastInsertId();
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

    /**
     * Exclui um cliente e todos os dados relacionados.
     * Delega para UsuariosSql::excluirConta (rotina única de exclusão, que remove
     * agendamentos, carrinho, pedidos, logs, notificações e códigos do usuário).
     * Retorna false se o id não existir ou for de um administrador.
     */
    public static function excluir(int $id): bool
    {
        return UsuariosSql::excluirConta($id);
    }
}

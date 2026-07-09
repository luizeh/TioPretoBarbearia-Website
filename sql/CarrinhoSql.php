<?php

include_once __DIR__ . '/../config/connection.php';

class CarrinhoSql
{
    public static function buscarOuCriar(int $usuarioId): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = :uid");
        $stmt->execute([':uid' => $usuarioId]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) return (int) $row['id'];

        $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id) VALUES (:uid)");
        $stmt->execute([':uid' => $usuarioId]);
        return (int) $pdo->lastInsertId();
    }

    public static function adicionarItem(int $carrinhoId, int $produtoId): void
    {
        $pdo  = Connection::getConnection();
        // Verifica se já existe
        $stmt = $pdo->prepare("SELECT id, quantidade FROM carrinho_itens WHERE carrinho_id = :cid AND produto_id = :pid");
        $stmt->execute([':cid' => $carrinhoId, ':pid' => $produtoId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = quantidade + 1 WHERE id = :id");
            $stmt->execute([':id' => $item['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO carrinho_itens (carrinho_id, produto_id, quantidade) VALUES (:cid, :pid, 1)");
            $stmt->execute([':cid' => $carrinhoId, ':pid' => $produtoId]);
        }
    }

    public static function atualizarQtd(int $itemId, int $carrinhoId, int $quantidade): void
    {
        $pdo = Connection::getConnection();
        if ($quantidade <= 0) {
            $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE id = :id AND carrinho_id = :cid");
            $stmt->execute([':id' => $itemId, ':cid' => $carrinhoId]);
        } else {
            $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = :qty WHERE id = :id AND carrinho_id = :cid");
            $stmt->execute([':qty' => $quantidade, ':id' => $itemId, ':cid' => $carrinhoId]);
        }
    }

    public static function removerItem(int $itemId, int $carrinhoId): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE id = :id AND carrinho_id = :cid");
        $stmt->execute([':id' => $itemId, ':cid' => $carrinhoId]);
    }

    public static function limpar(int $carrinhoId): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = :cid");
        $stmt->execute([':cid' => $carrinhoId]);
    }

    public static function listarItens(int $carrinhoId): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT ci.id, ci.produto_id, ci.quantidade,
                   p.nome, p.preco, p.foto_url,
                   (ci.quantidade * p.preco) AS subtotal
            FROM carrinho_itens ci
            JOIN produtos p ON p.id = ci.produto_id
            WHERE ci.carrinho_id = :cid
            ORDER BY ci.id
        ");
        $stmt->execute([':cid' => $carrinhoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarItens(int $carrinhoId): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantidade), 0) FROM carrinho_itens WHERE carrinho_id = :cid");
        $stmt->execute([':cid' => $carrinhoId]);
        return (int) $stmt->fetchColumn();
    }
}

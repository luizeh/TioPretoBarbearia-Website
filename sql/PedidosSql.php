<?php

include_once __DIR__ . '/../config/connection.php';

class PedidosSql
{
    public static function criar(int $usuarioId, string $endereco, array $itens): int
    {
        $pdo = Connection::getConnection();
        $total = array_sum(array_column($itens, 'subtotal'));

        $stmt = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, endereco, valor_total, status)
            VALUES (:uid, :endereco, :total, 'recebido')
        ");
        $stmt->execute([':uid' => $usuarioId, ':endereco' => $endereco, ':total' => $total]);
        $pedidoId = (int) $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("
            INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco)
            VALUES (:pid, :prod_id, :qty, :preco)
        ");
        foreach ($itens as $item) {
            $stmtItem->execute([
                ':pid'     => $pedidoId,
                ':prod_id' => (int) $item['produto_id'],
                ':qty'     => (int) $item['quantidade'],
                ':preco'   => (float) $item['preco'],
            ]);
        }

        return $pedidoId;
    }

    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, endereco, valor_total, status,
                   DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') AS data_fmt
            FROM pedidos
            WHERE usuario_id = :uid
            ORDER BY created_at DESC
        ");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

include_once __DIR__ . '/../config/connection.php';

class PedidosSql
{
    /**
     * Cria um pedido. $endereco é um array validado com as chaves:
     *   completo, cep, logradouro, numero, bairro, cidade, estado,
     *   complemento, ponto_referencia
     * A coluna `endereco` recebe o endereço completo formatado (compatibilidade)
     * e as colunas separadas guardam cada parte.
     */
    public static function criar(int $usuarioId, array $endereco, array $itens): int
    {
        $pdo = Connection::getConnection();
        try {
            $pdo->beginTransaction();
            $total = 0.0;
            $produtos = [];
            $buscarProduto = $pdo->prepare('SELECT id, preco, estoque FROM produtos WHERE id = :id FOR UPDATE');

            foreach ($itens as $item) {
                $produtoId = (int) $item['produto_id'];
                $quantidade = (int) $item['quantidade'];
                $buscarProduto->execute([':id' => $produtoId]);
                $produto = $buscarProduto->fetch(PDO::FETCH_ASSOC);
                if (!$produto) throw new RuntimeException('Um produto do carrinho não está mais disponível.');
                if ($quantidade < 1 || $quantidade > (int) $produto['estoque']) throw new RuntimeException('Estoque insuficiente para um dos produtos.');
                $preco = (float) $produto['preco'];
                $total += $preco * $quantidade;
                $produtos[] = ['id' => $produtoId, 'quantidade' => $quantidade, 'preco' => $preco];
            }

            $stmt = $pdo->prepare(
                "INSERT INTO pedidos
                    (usuario_id, endereco, cep, logradouro, numero, bairro, cidade, estado, complemento, ponto_referencia, valor_total, status)
                 VALUES
                    (:uid, :endereco, :cep, :logradouro, :numero, :bairro, :cidade, :estado, :complemento, :ponto_referencia, :total, 'recebido')"
            );
            $stmt->execute([
                ':uid'              => $usuarioId,
                ':endereco'         => $endereco['completo'] ?? '',
                ':cep'              => $endereco['cep'] ?? null,
                ':logradouro'       => $endereco['logradouro'] ?? null,
                ':numero'           => $endereco['numero'] ?? null,
                ':bairro'           => $endereco['bairro'] ?? null,
                ':cidade'           => $endereco['cidade'] ?? null,
                ':estado'           => $endereco['estado'] ?? null,
                ':complemento'      => ($endereco['complemento'] ?? '') !== '' ? $endereco['complemento'] : null,
                ':ponto_referencia' => ($endereco['ponto_referencia'] ?? '') !== '' ? $endereco['ponto_referencia'] : null,
                ':total'            => $total,
            ]);
            $pedidoId = (int) $pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco) VALUES (:pid, :prod_id, :qty, :preco)');
            $estoqueStmt = $pdo->prepare('UPDATE produtos SET estoque = estoque - :qty WHERE id = :id');
            foreach ($produtos as $produto) {
                $itemStmt->execute([':pid' => $pedidoId, ':prod_id' => $produto['id'], ':qty' => $produto['quantidade'], ':preco' => $produto['preco']]);
                $estoqueStmt->execute([':qty' => $produto['quantidade'], ':id' => $produto['id']]);
            }
            $pdo->commit();
            return $pedidoId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, endereco, valor_total, status, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') AS data_fmt FROM pedidos WHERE usuario_id = :uid ORDER BY created_at DESC");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarTodos(int $limite = 200): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT p.id, p.usuario_id, CONCAT(u.nome, ' ', u.sobrenome) AS cliente,
                u.telefone, p.endereco, p.valor_total, p.status,
                DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS data_fmt,
                COALESCE(GROUP_CONCAT(CONCAT(pr.nome, ' (', pi.quantidade, 'x)') SEPARATOR ', '), 'Sem itens') AS itens
            FROM pedidos p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN pedido_itens pi ON pi.pedido_id = p.id
            LEFT JOIN produtos pr ON pr.id = pi.produto_id
            GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limite");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function atualizarStatus(int $id, string $status): bool
    {
        $permitidos = ['recebido', 'preparando', 'pronto', 'entregue', 'cancelado'];
        if (!in_array($status, $permitidos, true)) throw new InvalidArgumentException('Status de pedido inválido.');
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('UPDATE pedidos SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}

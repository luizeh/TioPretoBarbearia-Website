<?php

include_once __DIR__ . '/../config/Connection.php';

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
            // Só produtos ativos podem entrar em novos pedidos (ativo = 1).
            $buscarProduto = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE id = :id AND ativo = 1 FOR UPDATE');

            foreach ($itens as $item) {
                $produtoId = (int) $item['produto_id'];
                $quantidade = (int) $item['quantidade'];
                $buscarProduto->execute([':id' => $produtoId]);
                $produto = $buscarProduto->fetch(PDO::FETCH_ASSOC);
                if (!$produto) throw new RuntimeException('Um produto do carrinho não está mais disponível.');
                if ($quantidade < 1 || $quantidade > (int) $produto['estoque']) throw new RuntimeException('Estoque insuficiente para um dos produtos.');
                $preco = (float) $produto['preco'];
                $total += $preco * $quantidade;
                // Guarda o nome atual como snapshot histórico do item.
                $produtos[] = ['id' => $produtoId, 'nome' => (string) $produto['nome'], 'quantidade' => $quantidade, 'preco' => $preco];
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
            $itemStmt = $pdo->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, produto_nome, quantidade, preco) VALUES (:pid, :prod_id, :prod_nome, :qty, :preco)');
            $estoqueStmt = $pdo->prepare('UPDATE produtos SET estoque = estoque - :qty WHERE id = :id');
            foreach ($produtos as $produto) {
                $itemStmt->execute([':pid' => $pedidoId, ':prod_id' => $produto['id'], ':prod_nome' => $produto['nome'], ':qty' => $produto['quantidade'], ':preco' => $produto['preco']]);
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
                COALESCE(GROUP_CONCAT(CONCAT(COALESCE(pi.produto_nome, pr.nome, 'Produto removido'), ' (', pi.quantidade, 'x)') SEPARATOR ', '), 'Sem itens') AS itens
            FROM pedidos p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN pedido_itens pi ON pi.pedido_id = p.id
            LEFT JOIN produtos pr ON pr.id = pi.produto_id
            GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limite");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Altera o status do pedido reconciliando o estoque:
     *  - o estoque fica reservado em todos os status, EXCETO 'cancelado';
     *  - ao cancelar (sair de um status que reservava), os itens voltam ao estoque;
     *  - ao reativar (sair de 'cancelado'), os itens são baixados de novo
     *    (com verificação de disponibilidade).
     */
    public static function atualizarStatus(int $id, string $novoStatus): bool
    {
        $permitidos = ['recebido', 'preparando', 'pronto', 'entregue', 'cancelado'];
        if (!in_array($novoStatus, $permitidos, true)) throw new InvalidArgumentException('Status de pedido inválido.');

        $pdo = Connection::getConnection();
        try {
            $pdo->beginTransaction();

            // Trava o pedido e lê o status atual.
            $stmtAtual = $pdo->prepare('SELECT status FROM pedidos WHERE id = :id FOR UPDATE');
            $stmtAtual->execute([':id' => $id]);
            $statusAtual = $stmtAtual->fetchColumn();

            if ($statusAtual === false) {
                $pdo->commit();
                return false; // pedido inexistente
            }

            if ($statusAtual !== $novoStatus) {
                $reservavaAntes = self::consomeEstoque($statusAtual);
                $reservaAgora   = self::consomeEstoque($novoStatus);

                if ($reservavaAntes && !$reservaAgora) {
                    self::ajustarEstoquePorPedido($pdo, $id, +1); // devolve ao estoque
                } elseif (!$reservavaAntes && $reservaAgora) {
                    self::ajustarEstoquePorPedido($pdo, $id, -1); // baixa do estoque
                }
            }

            $upd = $pdo->prepare('UPDATE pedidos SET status = :status WHERE id = :id');
            $upd->execute([':status' => $novoStatus, ':id' => $id]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /** O estoque é reservado em todos os status, menos 'cancelado'. */
    private static function consomeEstoque(string $status): bool
    {
        return $status !== 'cancelado';
    }

    /**
     * Ajusta o estoque de todos os itens de um pedido.
     * $sinal = +1 devolve ao estoque; -1 baixa (verificando disponibilidade).
     */
    private static function ajustarEstoquePorPedido(PDO $pdo, int $pedidoId, int $sinal): void
    {
        $stmtItens = $pdo->prepare('SELECT produto_id, quantidade FROM pedido_itens WHERE pedido_id = :pid');
        $stmtItens->execute([':pid' => $pedidoId]);
        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        // Ao baixar, garante que há estoque suficiente para cada item.
        if ($sinal < 0) {
            $stmtCheck = $pdo->prepare('SELECT nome, estoque FROM produtos WHERE id = :id FOR UPDATE');
            foreach ($itens as $item) {
                $stmtCheck->execute([':id' => (int) $item['produto_id']]);
                $produto = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if ($produto && (int) $item['quantidade'] > (int) $produto['estoque']) {
                    throw new RuntimeException('Estoque insuficiente para reativar o pedido (produto: ' . $produto['nome'] . ').');
                }
            }
        }

        $stmtAjuste = $pdo->prepare('UPDATE produtos SET estoque = estoque + :delta WHERE id = :id');
        foreach ($itens as $item) {
            $stmtAjuste->bindValue(':delta', $sinal * (int) $item['quantidade'], PDO::PARAM_INT);
            $stmtAjuste->bindValue(':id', (int) $item['produto_id'], PDO::PARAM_INT);
            $stmtAjuste->execute();
        }
    }
}

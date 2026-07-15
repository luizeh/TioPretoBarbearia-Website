<?php

class ProdutosSql
{
    /**
     * Lista produtos. Se $apenasVisiveis for true, retorna só os marcados
     * como visíveis no site (usado pelo catálogo público).
     */
    public static function listarProdutos($pdo, bool $apenasVisiveis = false): array
    {
        $where = $apenasVisiveis ? 'WHERE p.visivel = 1' : '';
        $sql = "
            SELECT
                p.id,
                p.nome,
                p.foto_url,
                p.descricao,
                p.preco,
                p.estoque,
                p.visivel,
                GROUP_CONCAT(DISTINCT t.nome ORDER BY t.nome SEPARATOR ', ') AS tags,
                GROUP_CONCAT(DISTINCT t.id   ORDER BY t.id   SEPARATOR ',') AS tag_ids
            FROM produtos p
            LEFT JOIN produto_tags pt ON pt.produto_id = p.id
            LEFT JOIN tags t ON t.id = pt.tag_id
            $where
            GROUP BY p.id
            ORDER BY p.nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Define se um produto aparece no site (1) ou fica só para admins (0).
     */
    public static function definirVisibilidade($pdo, int $id, bool $visivel): void
    {
        $stmt = $pdo->prepare("UPDATE produtos SET visivel = :visivel WHERE id = :id");
        $stmt->execute([':visivel' => $visivel ? 1 : 0, ':id' => $id]);
    }

    public static function adicionarProdutos($pdo, array $dados = [])
    {
        // Suporte a chamada via API (array $dados) ou legado ($_POST)
        $nome     = $dados['nome']     ?? $_POST['nome']     ?? '';
        $descricao = $dados['descricao'] ?? $_POST['descricao'] ?? null;
        $preco    = $dados['preco']    ?? $_POST['preco']    ?? 0;
        $estoque  = $dados['estoque']  ?? $_POST['estoque']  ?? 0;
        $foto_url = $dados['foto_url'] ?? $_POST['foto_url'] ?? null;

        $sql = "
            INSERT INTO produtos (nome, descricao, preco, estoque, foto_url)
            VALUES (:nome, :descricao, :preco, :estoque, :foto_url)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $descricao,
            ':preco'     => $preco,
            ':estoque'   => (int) $estoque,
            ':foto_url'  => $foto_url,
        ]);

        return $pdo->lastInsertId();
    }

    public static function adicionarTagProduto($pdo, $idProduto, $idTag)
    {
        $sql = "
            INSERT INTO produto_tags
            (
                produto_id,
                tag_id
            )
            VALUES
            (
                :produto_id,
                :tag_id
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":produto_id" => $idProduto,
            ":tag_id"     => $idTag
        ]);
    }

    public static function listarTags($pdo, string $ordenarPor = 'nome')
    {
        // Whitelist da coluna de ordenação (evita SQL injection).
        $coluna = $ordenarPor === 'id' ? 'id' : 'nome';

        $sql = "
            SELECT
                id,
                nome
            FROM tags
            ORDER BY {$coluna}
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function excluirProduto($pdo, $idProduto)
    {
        $idProduto = (int) $idProduto;

        try {
            $pdo->beginTransaction();

            // Produtos que já fazem parte de pedidos não podem ser excluídos
            // (preservar o histórico de vendas). Sugere ocultar em vez de excluir.
            $check = $pdo->prepare("SELECT COUNT(*) FROM pedido_itens WHERE produto_id = :id");
            $check->execute([":id" => $idProduto]);
            if ((int) $check->fetchColumn() > 0) {
                throw new RuntimeException('Este produto já faz parte de pedidos e não pode ser excluído. Oculte-o do site em vez de excluir.');
            }

            // Remove vínculos transitórios antes de excluir o produto
            // (carrinhos de clientes e tags associadas).
            $pdo->prepare("DELETE FROM carrinho_itens WHERE produto_id = :id")->execute([":id" => $idProduto]);
            $pdo->prepare("DELETE FROM produto_tags WHERE produto_id = :id")->execute([":id" => $idProduto]);
            $pdo->prepare("DELETE FROM produtos WHERE id = :id")->execute([":id" => $idProduto]);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function buscarProdutoPorId($pdo, $id)
    {
        $sql = "
            SELECT *
            FROM produtos
            WHERE id = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function editarProduto($pdo, $id, array $dados = [])
    {
        $nome     = $dados['nome']     ?? $_POST['nome']     ?? '';
        $descricao = $dados['descricao'] ?? $_POST['descricao'] ?? null;
        $preco    = $dados['preco']    ?? $_POST['preco']    ?? 0;
        $estoque  = $dados['estoque']  ?? $_POST['estoque']  ?? 0;
        $foto_url = $dados['foto_url'] ?? $_POST['foto_url'] ?? null;
        $tags     = $dados['tags']     ?? null;

        $sql = "
            UPDATE produtos
            SET
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                estoque = :estoque,
                foto_url = :foto_url
            WHERE id = :id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $descricao,
            ':preco'     => $preco,
            ':estoque'   => (int) $estoque,
            ':foto_url'  => $foto_url,
            ':id'        => $id,
        ]);

        // Re-sync tags
        $delStmt = $pdo->prepare("DELETE FROM produto_tags WHERE produto_id = :id");
        $delStmt->execute([':id' => $id]);

        $tagList = $tags ?? ($_POST['tags'] ?? []);
        if (!empty($tagList)) {
            if (is_string($tagList)) {
                $tagList = array_filter(array_map('trim', explode(',', $tagList)));
            }
            foreach ($tagList as $idTag) {
                self::adicionarTagProduto($pdo, $id, $idTag);
            }
        }
    }
}

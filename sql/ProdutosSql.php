<?php


class ProdutosSql
{

    public static function listarProdutos($pdo): array
    {
        $stmt = $pdo->query("
        SELECT
            id,
            nome,
            foto_url,
            descricao,
            preco,
            estoque
        FROM produtos
        ORDER BY nome
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function adicionarProdutos($pdo)
    {
        $sql = "INSERT INTO produtos (nome, descricao, preco, foto_url) VALUES (:nome, :descricao, :preco, :foto_url)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":nome" => $_POST['nome'],
            ":descricao" => $_POST['descricao'],
            ":preco" => $_POST['preco'],
            ":foto_url" => $_POST['foto_url']

        ]);

        return $pdo->lastInsertId();
    }

    public static function adicionarTagProduto($pdo, $idProduto, $idTag)
    {
        $sql = "INSERT INTO produto_tags
            (produto_id, tag_id)
            VALUES
            (:produto_id, :tag_id)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":produto_id" => $idProduto,
            ":tag_id" => $idTag
        ]);
    }
}

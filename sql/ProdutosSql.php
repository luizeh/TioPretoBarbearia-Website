<?php

class ProdutosSql
{
    public static function listarProdutos($pdo): array
    {
        $sql = "
            SELECT
                p.id,
                p.nome,
                p.foto_url,
                p.descricao,
                p.preco,
                p.estoque,
                GROUP_CONCAT(t.nome SEPARATOR ', ') AS tags
            FROM produtos p
            LEFT JOIN produto_tags pt ON pt.produto_id = p.id
            LEFT JOIN tags t ON t.id = pt.tag_id
            GROUP BY p.id
            ORDER BY p.nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function adicionarProdutos($pdo)
    {
        $sql = "
            INSERT INTO produtos
            (
                nome,
                descricao,
                preco,
                estoque,
                foto_url
            )
            VALUES
            (
                :nome,
                :descricao,
                :preco,
                :estoque,
                :foto_url
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":nome"      => $_POST['nome'],
            ":descricao" => $_POST['descricao'],
            ":preco"     => $_POST['preco'],
            ":estoque"   => $_POST['estoque'],
            ":foto_url"  => $_POST['foto_url']
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

    public static function listarTags($pdo)
    {
        $sql = "
            SELECT
                id,
                nome
            FROM tags
            ORDER BY nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function excluirProduto($pdo, $idProduto)
    {
        $sql = "DELETE FROM produto_tags WHERE produto_id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $idProduto
        ]);

        $sql = "DELETE FROM produtos WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $idProduto
        ]);
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

    public static function editarProduto($pdo, $id)
    {
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
            ":nome"      => $_POST['nome'],
            ":descricao" => $_POST['descricao'],
            ":preco"     => $_POST['preco'],
            ":estoque"   => $_POST['estoque'],
            ":foto_url"  => $_POST['foto_url'],
            ":id"        => $id
        ]);

        $sql = "DELETE FROM produto_tags WHERE produto_id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        if (!empty($_POST['tags'])) {

            foreach ($_POST['tags'] as $idTag) {

                self::adicionarTagProduto($pdo, $id, $idTag);

            }

        }
    }
}
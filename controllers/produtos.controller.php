<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../config/connection.php');
include_once(__DIR__ . '/../sql/ProdutosSql.php');

$pdo = Connection::getConnection();

$produtos = ProdutosSql::listarProdutos($pdo);
$tags = ProdutosSql::listarTags($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
    $idProduto = ProdutosSql::adicionarProdutos($pdo);

    if (!empty($_POST['tags'])) {
        foreach ($_POST['tags'] as $idTag) {
            ProdutosSql::adicionarTagProduto($pdo, $idProduto, $idTag);
        }
    }
    header("Location: ../views/pages/produtos.php");
    exit;
}

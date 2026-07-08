<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../config/connection.php');
include_once(__DIR__ . '/../sql/ProdutosSql.php');

$pdo = Connection::getConnection();

$produtos = ProdutosSql::listarProdutos($pdo);

// Inserção só ocorre quando o formulário for submetido via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
    $idproduto = ProdutosSql::adicionarProdutos($pdo);
    if (!empty($_POST['tags'])) {
        foreach (explode(',', $_POST['tags']) as $tag) {
            $tag = trim($tag);
            if ($tag !== '') {
                ProdutosSql::adicionarTagProduto($pdo, $idproduto, $tag);
            }
        }
    }
}

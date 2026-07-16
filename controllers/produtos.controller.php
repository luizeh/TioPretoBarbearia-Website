<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../config/Connection.php');
include_once(__DIR__ . '/../sql/ProdutosSql.php');

$pdo = Connection::getConnection();

$produtos = ProdutosSql::listarProdutos($pdo);
$tags     = ProdutosSql::listarTags($pdo, 'id');

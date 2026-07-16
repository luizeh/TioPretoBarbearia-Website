<?php

/**
 * api/admin/produtos.php
 * CRUD de produtos — área admin.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/Connection.php';
require_once __DIR__ . '/../../sql/ProdutosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$pdo    = Connection::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $produtos = ProdutosSql::listarProdutos($pdo);
    helpers::resposta_json(true, 'OK', $produtos, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    helpers::verificarCsrf($body);
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        if (empty($body['nome'])) {
            helpers::resposta_json(false, 'O nome do produto é obrigatório.', null, 400);
        }
        $idProduto = ProdutosSql::adicionarProdutos($pdo, $body);
        // Sync tags
        if (!empty($body['tags'])) {
            $tagList = is_string($body['tags'])
                ? array_filter(array_map('trim', explode(',', $body['tags'])))
                : (array) $body['tags'];
            foreach ($tagList as $idTag) {
                ProdutosSql::adicionarTagProduto($pdo, $idProduto, $idTag);
            }
        }
        helpers::resposta_json(true, 'Produto criado com sucesso.', ['id' => $idProduto], 201);
    }

    if ($action === 'editar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do produto é obrigatório.', null, 400);
        }
        ProdutosSql::editarProduto($pdo, (int) $body['id'], $body);
        helpers::resposta_json(true, 'Produto atualizado com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do produto é obrigatório.', null, 400);
        }
        try {
            ProdutosSql::excluirProduto($pdo, (int) $body['id']);
        } catch (RuntimeException $e) {
            helpers::resposta_json(false, $e->getMessage(), null, 409);
        } catch (Throwable $e) {
            error_log('Excluir produto: ' . $e->getMessage());
            helpers::resposta_json(false, 'Não foi possível excluir o produto.', null, 500);
        }
        helpers::resposta_json(true, 'Produto excluído com sucesso.', null, 200);
    }

    if ($action === 'visibilidade') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do produto é obrigatório.', null, 400);
        }
        $visivel = !empty($body['visivel']);
        ProdutosSql::definirVisibilidade($pdo, (int) $body['id'], $visivel);
        helpers::resposta_json(
            true,
            $visivel ? 'Produto agora está visível no site.' : 'Produto oculto — visível apenas para admins.',
            ['id' => (int) $body['id'], 'visivel' => $visivel ? 1 : 0],
            200
        );
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

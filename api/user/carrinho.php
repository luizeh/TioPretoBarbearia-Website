<?php

/**
 * api/user/carrinho.php
 * GET              → lista itens do carrinho
 * POST action=adicionar  → adiciona produto (body: {produto_id})
 * POST action=atualizar  → atualiza quantidade (body: {item_id, quantidade})
 * POST action=remover    → remove item (body: {item_id})
 * POST action=limpar     → esvazia o carrinho
 */

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/../../sql/CarrinhoSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$usuarioId  = (int) $_SESSION['usuario_id'];
$carrinhoId = CarrinhoSql::buscarOuCriar($usuarioId);
$method     = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $itens = CarrinhoSql::listarItens($carrinhoId);
    $total = array_sum(array_column($itens, 'subtotal'));
    $count = CarrinhoSql::contarItens($carrinhoId);
    helpers::resposta_json(true, 'OK', ['itens' => $itens, 'total' => $total, 'count' => $count], 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'adicionar') {
        $produtoId = (int) ($body['produto_id'] ?? 0);
        if (!$produtoId) helpers::resposta_json(false, 'produto_id obrigatório.', null, 400);
        CarrinhoSql::adicionarItem($carrinhoId, $produtoId);
        $count = CarrinhoSql::contarItens($carrinhoId);
        helpers::resposta_json(true, 'Item adicionado.', ['count' => $count], 200);
    }

    if ($action === 'atualizar') {
        $itemId = (int) ($body['item_id'] ?? 0);
        $qty    = (int) ($body['quantidade'] ?? 0);
        if (!$itemId) helpers::resposta_json(false, 'item_id obrigatório.', null, 400);
        CarrinhoSql::atualizarQtd($itemId, $carrinhoId, $qty);
        $itens = CarrinhoSql::listarItens($carrinhoId);
        $total = array_sum(array_column($itens, 'subtotal'));
        $count = CarrinhoSql::contarItens($carrinhoId);
        helpers::resposta_json(true, 'Atualizado.', ['itens' => $itens, 'total' => $total, 'count' => $count], 200);
    }

    if ($action === 'remover') {
        $itemId = (int) ($body['item_id'] ?? 0);
        if (!$itemId) helpers::resposta_json(false, 'item_id obrigatório.', null, 400);
        CarrinhoSql::removerItem($itemId, $carrinhoId);
        $itens = CarrinhoSql::listarItens($carrinhoId);
        $total = array_sum(array_column($itens, 'subtotal'));
        $count = CarrinhoSql::contarItens($carrinhoId);
        helpers::resposta_json(true, 'Item removido.', ['itens' => $itens, 'total' => $total, 'count' => $count], 200);
    }

    if ($action === 'limpar') {
        CarrinhoSql::limpar($carrinhoId);
        helpers::resposta_json(true, 'Carrinho limpo.', ['itens' => [], 'total' => 0, 'count' => 0], 200);
    }
}

helpers::resposta_json(false, 'Ação inválida.', null, 400);

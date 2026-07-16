<?php

/**
 * api/user/pedidos.php
 * POST action=finalizar → cria pedido com itens do carrinho (body: {endereco})
 * GET                   → lista pedidos do usuário
 */

require_once __DIR__ . '/../../helpers/helpers.php';
helpers::iniciarSessao();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/../../sql/CarrinhoSql.php';
require_once __DIR__ . '/../../sql/PedidosSql.php';
require_once __DIR__ . '/../../sql/LogsSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$usuarioId  = (int) $_SESSION['usuario_id'];
$carrinhoId = CarrinhoSql::buscarOuCriar($usuarioId);
$method     = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $pedidos = PedidosSql::listarPorUsuario($usuarioId);
    helpers::resposta_json(true, 'OK', $pedidos, 200);
}

if ($method === 'POST') {
    $body    = json_decode(file_get_contents('php://input'), true) ?? [];
    $action  = $body['action'] ?? '';

    if ($action === 'finalizar') {
        helpers::verificarCsrf($body);

        // Validação de cada campo do endereço (revalida tudo no servidor).
        $endereco = [
            'cep'              => helpers::validarCep($body['cep'] ?? ''),
            'logradouro'       => helpers::validarObrigatorio($body['logradouro'] ?? '', 'logradouro', 2, 150),
            'numero'           => helpers::validarObrigatorio($body['numero'] ?? '', 'número', 1, 20),
            'bairro'           => helpers::validarObrigatorio($body['bairro'] ?? '', 'bairro', 2, 100),
            'cidade'           => helpers::validarObrigatorio($body['cidade'] ?? '', 'cidade', 2, 100),
            'estado'           => helpers::validarUf($body['estado'] ?? ''),
            'complemento'      => helpers::validarOpcional($body['complemento'] ?? '', 'complemento', 150),
            'ponto_referencia' => helpers::validarOpcional($body['ponto_referencia'] ?? '', 'ponto de referência', 150),
        ];

        // Monta o endereço completo (mantém compatibilidade com a coluna `endereco`).
        $completo = $endereco['logradouro'] . ', ' . $endereco['numero']
            . ' - ' . $endereco['bairro']
            . ', ' . $endereco['cidade'] . '/' . $endereco['estado']
            . ' - CEP: ' . $endereco['cep'];
        if ($endereco['complemento'] !== '') {
            $completo .= ' - Compl.: ' . $endereco['complemento'];
        }
        if ($endereco['ponto_referencia'] !== '') {
            $completo .= ' - Ref.: ' . $endereco['ponto_referencia'];
        }
        $endereco['completo'] = $completo;

        $itens = CarrinhoSql::listarItens($carrinhoId);
        if (empty($itens)) {
            helpers::resposta_json(false, 'Seu carrinho está vazio.', null, 400);
        }

        try {
            $pedidoId = PedidosSql::criar($usuarioId, $endereco, $itens);
        } catch (RuntimeException $e) {
            helpers::resposta_json(false, $e->getMessage(), null, 409);
        }
        CarrinhoSql::limpar($carrinhoId);
        LogsSql::registrar($usuarioId, 'pedido_criado', "Pedido #$pedidoId criado pelo cliente.");

        helpers::resposta_json(true, 'Pedido realizado com sucesso! Aguarde a confirmação.', ['pedido_id' => $pedidoId], 201);
    }
}

helpers::resposta_json(false, 'Ação inválida.', null, 400);

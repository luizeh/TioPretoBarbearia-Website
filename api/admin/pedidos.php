<?php
require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../sql/PedidosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/LogsSql.php';
require_once __DIR__ . '/../../sql/NotificacoesSql.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    helpers::resposta_json(true, 'OK', PedidosSql::listarTodos());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int) ($body['id'] ?? 0);
    $status = $body['status'] ?? '';
    if (!$id || !$status) helpers::resposta_json(false, 'Pedido e status são obrigatórios.', null, 400);
    $pedido = null;
    foreach (PedidosSql::listarTodos() as $item) if ((int) $item['id'] === $id) { $pedido = $item; break; }
    if (!$pedido) helpers::resposta_json(false, 'Pedido não encontrado.', null, 404);
    PedidosSql::atualizarStatus($id, $status);
    LogsSql::registrar((int) $_SESSION['usuario_id'], 'pedido_status', "Status do pedido #$id alterado para $status.");
    NotificacoesSql::criar((int) $pedido['usuario_id'], 'pedido', 'Status do pedido atualizado', 'O status do seu pedido foi alterado para: ' . ucfirst($status) . '.');
    helpers::resposta_json(true, 'Status do pedido atualizado.');
}
helpers::resposta_json(false, 'Método não reconhecido.', null, 405);

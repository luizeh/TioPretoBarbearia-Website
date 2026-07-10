<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/NotificacoesSql.php';
helpers::verificar_login();
$usuarioId = (int) $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    helpers::resposta_json(true, 'OK', ['itens' => NotificacoesSql::listarPorUsuario($usuarioId), 'nao_lidas' => NotificacoesSql::contarNaoLidas($usuarioId)]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (($body['action'] ?? '') === 'marcar_todas') {
        NotificacoesSql::marcarTodasComoLidas($usuarioId);
        helpers::resposta_json(true, 'Notificações marcadas como lidas.');
    }
    if (($body['action'] ?? '') === 'marcar_lida' && !empty($body['id'])) {
        NotificacoesSql::marcarComoLida((int) $body['id'], $usuarioId);
        helpers::resposta_json(true, 'Notificação marcada como lida.');
    }
}
helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);

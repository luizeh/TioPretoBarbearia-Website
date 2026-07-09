<?php

/**
 * api/user/slots.php
 * GET ?data=2026-07-09
 * Retorna os slots de horário do dia para o cliente:
 *  - livre: pode agendar
 *  - ocupado: tomado por outro cliente (nome omitido)
 *  - meu: agendamento próprio (inclui id e serviço)
 */

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$data = trim($_GET['data'] ?? '');
if (!$data || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    helpers::resposta_json(false, 'Data inválida.', null, 400);
}

$usuarioId = (int) $_SESSION['usuario_id'];
$slots     = AgendamentosSql::listarSlotsPorData($data, $usuarioId);

helpers::resposta_json(true, 'OK', $slots, 200);

<?php

/**
 * api/admin/session_admin.php
 * Verificação de sessão para endpoints da área admin.
 * Retorna JSON 401/403 em vez de redirecionar.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

if (empty($_SESSION['usuario_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

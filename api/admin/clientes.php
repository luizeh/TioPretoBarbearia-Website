<?php

/**
 * api/admin/clientes.php
 * Listagem, edição e exclusão de clientes — área admin.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../sql/ClientesSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $clientes = ClientesSql::listar(200, 0);
    helpers::resposta_json(true, 'OK', $clientes, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'editar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do cliente é obrigatório.', null, 400);
        }
        $required = ['nome', 'sobrenome', 'email'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        ClientesSql::editar((int) $body['id'], $body);
        helpers::resposta_json(true, 'Cliente atualizado com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do cliente é obrigatório.', null, 400);
        }
        ClientesSql::excluir((int) $body['id']);
        helpers::resposta_json(true, 'Cliente excluído com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

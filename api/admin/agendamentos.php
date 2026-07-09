<?php

/**
 * api/admin/agendamentos.php
 * CRUD de agendamentos — área admin.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $agendamentos = AgendamentosSql::listarTodos(200, 0);
    helpers::resposta_json(true, 'OK', $agendamentos, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        $required = ['usuario_id', 'servico_id', 'data', 'hora_inicio', 'hora_fim'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        $id = AgendamentosSql::criar($body);
        helpers::resposta_json(true, 'Agendamento criado com sucesso.', ['id' => $id], 201);
    }

    if ($action === 'editar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do agendamento é obrigatório.', null, 400);
        }
        AgendamentosSql::editar((int) $body['id'], $body);
        helpers::resposta_json(true, 'Agendamento atualizado com sucesso.', null, 200);
    }

    if ($action === 'status') {
        if (empty($body['id']) || empty($body['status'])) {
            helpers::resposta_json(false, 'ID e status são obrigatórios.', null, 400);
        }
        AgendamentosSql::editarStatus((int) $body['id'], $body['status']);
        helpers::resposta_json(true, 'Status atualizado com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do agendamento é obrigatório.', null, 400);
        }
        AgendamentosSql::excluir((int) $body['id']);
        helpers::resposta_json(true, 'Agendamento excluído com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

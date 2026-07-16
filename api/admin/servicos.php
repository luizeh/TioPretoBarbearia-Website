<?php

/**
 * api/admin/servicos.php
 * CRUD de serviços — área admin.
 * GET           → lista todos os serviços
 * POST action=criar  → cria serviço
 * POST action=editar → edita serviço
 * POST action=excluir → exclui serviço
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/Connection.php';
require_once __DIR__ . '/../../sql/ServicosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $servicos = ServicosSql::listar();
    helpers::resposta_json(true, 'OK', $servicos, 200);
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    helpers::verificarCsrf($body);
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        if (empty($body['nome']) || empty($body['preco']) || empty($body['tempo_estimado'])) {
            helpers::resposta_json(false, 'Campos obrigatórios: nome, preco, tempo_estimado.', null, 400);
        }
        $id = ServicosSql::criar($body);
        helpers::resposta_json(true, 'Serviço criado com sucesso.', ['id' => $id], 201);
    }

    if ($action === 'editar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do serviço é obrigatório.', null, 400);
        }
        ServicosSql::editar((int) $body['id'], $body);
        helpers::resposta_json(true, 'Serviço atualizado com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do serviço é obrigatório.', null, 400);
        }
        ServicosSql::excluir((int) $body['id']);
        helpers::resposta_json(true, 'Serviço excluído com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

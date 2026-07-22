<?php

/**
 * api/admin/clientes.php
 * Listagem, edição e exclusão de clientes — área admin.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/Connection.php';
require_once __DIR__ . '/../../sql/ClientesSql.php';
require_once __DIR__ . '/../../sql/UsuariosSql.php';
require_once __DIR__ . '/../../sql/DashboardSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/LogsSql.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $clientes = ClientesSql::listar(200, 0);
    helpers::resposta_json(true, 'OK', $clientes, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    // Regra: um admin promovido não pode alterar (editar/excluir) o admin que o
    // promoveu. Descobrimos quem promoveu o admin atual e bloqueamos ações cujo
    // alvo seja exatamente esse promotor.
    $adminAtualId = (int) ($_SESSION['usuario_id'] ?? 0);
    $adminAtual   = DashboardSql::buscarUsuario($adminAtualId);
    $meuPromotor  = $adminAtual ? (int) ($adminAtual['promovido_por'] ?? 0) : 0;
    $protegerPromotor = static function (int $alvoId) use ($meuPromotor): void {
        if ($meuPromotor > 0 && $alvoId === $meuPromotor) {
            helpers::resposta_json(false, 'Você não pode alterar o admin que promoveu você.', null, 403);
        }
    };

    if ($action === 'promover') {
        $alvoId = (int) ($body['id'] ?? 0);
        if ($alvoId <= 0) {
            helpers::resposta_json(false, 'ID do usuário é obrigatório.', null, 400);
        }
        $alvo = DashboardSql::buscarUsuario($alvoId);
        if (!$alvo) {
            helpers::resposta_json(false, 'Usuário não encontrado.', null, 404);
        }
        if (!empty($alvo['admin'])) {
            helpers::resposta_json(false, 'Este usuário já é administrador.', null, 409);
        }
        UsuariosSql::promover($alvoId, $adminAtualId);
        LogsSql::registrar($adminAtualId, 'admin_promovido', "Usuário #{$alvoId} promovido a administrador.");
        helpers::resposta_json(true, 'Usuário promovido a administrador.', null, 200);
    }

    if ($action === 'criar') {
        $required = ['nome', 'sobrenome', 'email', 'telefone', 'cidade'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        $body['telefone'] = helpers::normalizarTelefone($body['telefone']);
        $id = ClientesSql::criar($body);
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'conta_criada', "Conta do cliente #$id criada pelo administrador.");
        helpers::resposta_json(true, 'Cliente criado com sucesso.', ['id' => $id], 201);
    }

    if ($action === 'editar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do cliente é obrigatório.', null, 400);
        }
        $protegerPromotor((int) $body['id']);
        $required = ['nome', 'sobrenome', 'email'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        if (isset($body['telefone'])) $body['telefone'] = helpers::normalizarTelefone($body['telefone']);
        ClientesSql::editar((int) $body['id'], $body);
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'conta_editada', "Dados do cliente #{$body['id']} editados pelo administrador.");
        helpers::resposta_json(true, 'Cliente atualizado com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do cliente é obrigatório.', null, 400);
        }
        $clienteId = (int) $body['id'];
        $protegerPromotor($clienteId);
        try {
            if (!ClientesSql::excluir($clienteId)) {
                helpers::resposta_json(false, 'Cliente não encontrado ou não pode ser excluído.', null, 404);
            }
        } catch (Throwable $e) {
            error_log('admin clientes excluir: ' . $e->getMessage());
            helpers::resposta_json(false, 'Não foi possível excluir o cliente agora.', null, 500);
        }
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'conta_excluida', "Conta do cliente #{$clienteId} excluída pelo administrador.");
        helpers::resposta_json(true, 'Cliente excluído com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

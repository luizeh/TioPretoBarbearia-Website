<?php

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/LogsSql.php';

helpers::verificar_login();
$usuarioId = (int) $_SESSION['usuario_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'listar';
        if ($action === 'agenda') {
            $inicio = trim($_GET['inicio'] ?? '');
            $fim = trim($_GET['fim'] ?? '');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fim)) {
                helpers::resposta_json(false, 'Período inválido.', null, 400);
            }
            helpers::resposta_json(true, 'OK', AgendamentosSql::listarAgendaPrivada($inicio, $fim, $usuarioId));
        }
        if ($action === 'detalhar') {
            $agendamento = AgendamentosSql::buscarPorId((int) ($_GET['id'] ?? 0));
            if (!$agendamento || (int) $agendamento['usuario_id'] !== $usuarioId) {
                helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
            }
            $agendamento['servicos'] = AgendamentosSql::listarServicosDoAgendamento((int) $agendamento['id']);
            helpers::resposta_json(true, 'OK', $agendamento);
        }
        helpers::resposta_json(true, 'OK', AgendamentosSql::listarPorUsuario($usuarioId));
    }

    if ($method !== 'POST') helpers::resposta_json(false, 'Método não reconhecido.', null, 405);

    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        $body['usuario_id'] = $usuarioId;
        $body['status'] = 'pendente';
        $id = AgendamentosSql::salvarComServicos($body);
        LogsSql::registrar($usuarioId, 'agendamento_criado', "Agendamento #$id criado pelo cliente.");
        helpers::resposta_json(true, 'Agendamento criado com sucesso!', ['id' => $id], 201);
    }

    if ($action === 'editar') {
        $id = (int) ($body['id'] ?? 0);
        if (!$id) helpers::resposta_json(false, 'ID do agendamento é obrigatório.', null, 400);
        unset($body['status'], $body['usuario_id']);
        AgendamentosSql::salvarComServicos($body, $id, $usuarioId);
        LogsSql::registrar($usuarioId, 'agendamento_editado', "Agendamento #$id editado pelo cliente.");
        helpers::resposta_json(true, 'Agendamento atualizado com sucesso.');
    }

    if ($action === 'cancelar') {
        if (!AgendamentosSql::cancelarPorUsuario((int) ($body['id'] ?? 0), $usuarioId)) {
            helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
        }
        LogsSql::registrar($usuarioId, 'agendamento_cancelado', 'Agendamento #' . (int) $body['id'] . ' cancelado pelo cliente.');
        helpers::resposta_json(true, 'Agendamento cancelado com sucesso.');
    }

    if ($action === 'excluir') {
        if (!AgendamentosSql::excluirPorUsuario((int) ($body['id'] ?? 0), $usuarioId)) {
            helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
        }
        LogsSql::registrar($usuarioId, 'agendamento_excluido', 'Agendamento #' . (int) $body['id'] . ' excluído pelo cliente.');
        helpers::resposta_json(true, 'Agendamento excluído com sucesso.');
    }

    helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
} catch (InvalidArgumentException $e) {
    helpers::resposta_json(false, $e->getMessage(), null, 422);
} catch (RuntimeException $e) {
    helpers::resposta_json(false, $e->getMessage(), null, 409);
} catch (Throwable $e) {
    error_log($e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir o agendamento.', null, 500);
}

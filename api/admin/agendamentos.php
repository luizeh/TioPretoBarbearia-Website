<?php

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/LogsSql.php';
require_once __DIR__ . '/../../sql/NotificacoesSql.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        helpers::resposta_json(true, 'OK', AgendamentosSql::listarTodos(200, 0));
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') helpers::resposta_json(false, 'Método não reconhecido.', null, 405);

    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
    if ($action === 'criar') {
        $id = AgendamentosSql::salvarComServicos($body);
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'agendamento_criado', "Agendamento #$id criado pelo administrador.");
        NotificacoesSql::criar((int) $body['usuario_id'], 'agendamento', 'Novo agendamento', 'Um agendamento foi criado para você pelo administrador.');
        helpers::resposta_json(true, 'Agendamento criado com sucesso.', ['id' => $id], 201);
    }
    if ($action === 'editar') {
        $id = (int) ($body['id'] ?? 0);
        if (!$id) helpers::resposta_json(false, 'ID do agendamento é obrigatório.', null, 400);
        $anterior = AgendamentosSql::buscarPorId($id);
        AgendamentosSql::salvarComServicos($body, $id);
        $agendamento = AgendamentosSql::buscarPorId($id);
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'agendamento_editado', "Agendamento #$id editado pelo administrador.");
        if ($agendamento) {
            $foiConfirmado = ($agendamento['status'] ?? '') === 'confirmado' && ($anterior['status'] ?? '') !== 'confirmado';
            if ($foiConfirmado) {
                NotificacoesSql::criar((int) $agendamento['usuario_id'], 'confirmacao', 'Horário confirmado', 'O administrador confirmou o seu horário.');
            } else {
                NotificacoesSql::criar((int) $agendamento['usuario_id'], 'agendamento', 'Agendamento atualizado', 'O administrador atualizou os dados do seu agendamento.');
            }
        }
        helpers::resposta_json(true, 'Agendamento atualizado com sucesso.');
    }
    if ($action === 'status') {
        $id = (int) ($body['id'] ?? 0);
        $agendamento = AgendamentosSql::buscarPorId($id);
        if (!$agendamento) helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
        AgendamentosSql::editarStatus($id, $body['status'] ?? '');
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'agendamento_status', "Status do agendamento #$id alterado para {$body['status']}.");
        if ($agendamento) {
            $status = $body['status'];
            $titulo = $status === 'confirmado' ? 'Horário confirmado' : 'Status do agendamento atualizado';
            $mensagem = $status === 'confirmado' ? 'O administrador confirmou o seu horário.' : 'O status do seu agendamento foi alterado para: ' . ucfirst($status) . '.';
            NotificacoesSql::criar((int) $agendamento['usuario_id'], 'status', $titulo, $mensagem);
        }
        helpers::resposta_json(true, 'Status atualizado com sucesso.');
    }
    if ($action === 'excluir') {
        $id = (int) ($body['id'] ?? 0);
        $agendamento = AgendamentosSql::buscarPorId($id);
        if (!$agendamento) helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
        AgendamentosSql::excluir($id);
        LogsSql::registrar((int) $_SESSION['usuario_id'], 'agendamento_excluido', "Agendamento #$id excluído pelo administrador.");
        if ($agendamento) NotificacoesSql::criar((int) $agendamento['usuario_id'], 'exclusao', 'Agendamento excluído', 'Seu agendamento foi excluído pelo administrador.');
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

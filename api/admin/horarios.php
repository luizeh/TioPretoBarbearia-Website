<?php

/**
 * api/admin/horarios.php
 * Leitura e gravação dos horários de funcionamento — área admin.
 *
 * GET  → retorna todos os 7 dias do padrão semanal
 * POST action=salvar      → salva padrão semanal de um dia
 * POST action=excecao     → upsert de exceção por data específica
 * POST action=excecao_remover → remove exceção (restaura padrão)
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/HorariosSql.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'horarios';
    if ($action === 'bloqueios') {
        helpers::resposta_json(true, 'OK', HorariosSql::buscarTodosBloqueios());
    }
    helpers::resposta_json(true, 'OK', HorariosSql::buscarTodos());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? 'salvar';

    // ── Exceção por data específica ──────────────────────────────
    if ($action === 'excecao') {
        $data    = trim((string) ($body['data'] ?? ''));
        $fechado = !empty($body['fechado']);
        $abertura   = $fechado ? null : trim((string) ($body['abertura']   ?? ''));
        $fechamento = $fechado ? null : trim((string) ($body['fechamento'] ?? ''));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            helpers::resposta_json(false, 'Data inválida.', null, 400);
        }
        $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
        if (!$fechado && (!preg_match($reHora, $abertura) || !preg_match($reHora, $fechamento))) {
            helpers::resposta_json(false, 'Informe horários válidos no formato HH:MM.', null, 400);
        }
        if (!$fechado && $abertura >= $fechamento) {
            helpers::resposta_json(false, 'Abertura deve ser anterior ao fechamento.', null, 400);
        }
        // Normaliza para HH:MM:SS
        if ($abertura   && strlen($abertura)   === 5) $abertura   .= ':00';
        if ($fechamento && strlen($fechamento) === 5) $fechamento .= ':00';

        HorariosSql::salvarExcecao($data, $fechado, $abertura, $fechamento);
        helpers::resposta_json(true, 'Exceção salva com sucesso.');
    }

    if ($action === 'excecao_remover') {
        $data = trim((string) ($body['data'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            helpers::resposta_json(false, 'Data inválida.', null, 400);
        }
        HorariosSql::excluirExcecao($data);
        helpers::resposta_json(true, 'Exceção removida. Padrão da semana restaurado.');
    }

    if ($action === 'bloqueio_criar') {
        $diaSemana  = isset($body['dia_semana']) && $body['dia_semana'] !== '' && $body['dia_semana'] !== null
            ? (int) $body['dia_semana'] : null;
        $horaInicio = trim((string) ($body['hora_inicio'] ?? ''));
        $horaFim    = trim((string) ($body['hora_fim']    ?? ''));
        $descricao  = trim((string) ($body['descricao']   ?? '')) ?: null;

        $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
        if (!preg_match($reHora, $horaInicio) || !preg_match($reHora, $horaFim)) {
            helpers::resposta_json(false, 'Informe horários válidos (HH:MM).', null, 400);
        }
        if (strlen($horaInicio) === 5) $horaInicio .= ':00';
        if (strlen($horaFim)    === 5) $horaFim    .= ':00';
        if ($horaInicio >= $horaFim) {
            helpers::resposta_json(false, 'Início deve ser anterior ao fim.', null, 400);
        }
        if ($diaSemana !== null && ($diaSemana < 1 || $diaSemana > 7)) {
            helpers::resposta_json(false, 'Dia da semana inválido (1–7).', null, 400);
        }

        try {
            $id = HorariosSql::criarBloqueio($diaSemana, $horaInicio, $horaFim, $descricao);
        } catch (PDOException $e) {
            helpers::resposta_json(false, 'Tabela de bloqueios não encontrada. Execute o schema SQL no banco de dados.', null, 500);
        }
        helpers::resposta_json(true, 'Bloqueio criado com sucesso.', ['id' => $id], 201);
    }

    if ($action === 'bloqueio_excluir') {
        $id = (int) ($body['id'] ?? 0);
        if ($id <= 0) helpers::resposta_json(false, 'ID inválido.', null, 400);
        HorariosSql::excluirBloqueio($id);
        helpers::resposta_json(true, 'Bloqueio removido.');
    }
    $diaSemana  = (int) ($body['dia_semana'] ?? 0);
    $abertura   = trim((string) ($body['abertura']   ?? ''));
    $fechamento = trim((string) ($body['fechamento'] ?? ''));
    $fechado    = !empty($body['fechado']);

    if ($diaSemana < 1 || $diaSemana > 7) {
        helpers::resposta_json(false, 'Dia da semana inválido (1=Seg … 7=Dom).', null, 400);
    }
    $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
    if (!$fechado && (!preg_match($reHora, $abertura) || !preg_match($reHora, $fechamento))) {
        helpers::resposta_json(false, 'Informe horários válidos no formato HH:MM.', null, 400);
    }
    $abertura   = $fechado ? '08:00:00' : (strlen($abertura)   === 5 ? $abertura   . ':00' : $abertura);
    $fechamento = $fechado ? '20:00:00' : (strlen($fechamento) === 5 ? $fechamento . ':00' : $fechamento);
    if (!$fechado && $abertura >= $fechamento) {
        helpers::resposta_json(false, 'O horário de abertura deve ser anterior ao de fechamento.', null, 400);
    }

    HorariosSql::salvar($diaSemana, $abertura, $fechamento, $fechado);
    helpers::resposta_json(true, 'Horário salvo com sucesso.');
}

helpers::resposta_json(false, 'Método não permitido.', null, 405);

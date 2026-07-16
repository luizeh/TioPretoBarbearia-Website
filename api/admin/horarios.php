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

/**
 * Extrai os dias de exceção (ISO 1–7) do corpo. Só faz sentido quando o
 * bloqueio vale para "todos os dias" ($diaSemana === null); caso contrário,
 * retorna null. Devolve uma string CSV ("6" ou "6,7") ou null.
 */
function parseDiasExcecao(array $body, ?int $diaSemana): ?string
{
    if ($diaSemana !== null || !is_array($body['dias_excecao'] ?? null)) {
        return null;
    }
    $dias = array_values(array_unique(array_filter(
        array_map('intval', $body['dias_excecao']),
        static fn(int $d): bool => $d >= 1 && $d <= 7
    )));
    sort($dias);
    return $dias ? implode(',', $dias) : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'horarios';
    if ($action === 'bloqueios') {
        helpers::resposta_json(true, 'OK', HorariosSql::buscarTodosBloqueios());
    }
    if ($action === 'periodos') {
        helpers::resposta_json(true, 'OK', HorariosSql::buscarTodosPeriodos());
    }
    helpers::resposta_json(true, 'OK', HorariosSql::buscarTodos());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    helpers::verificarCsrf($body);
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
        $diasExcecao = parseDiasExcecao($body, $diaSemana);

        try {
            $id = HorariosSql::criarBloqueio($diaSemana, $horaInicio, $horaFim, $descricao, $diasExcecao);
        } catch (PDOException $e) {
            helpers::resposta_json(false, 'Tabela de bloqueios não encontrada. Execute o schema SQL no banco de dados.', null, 500);
        }
        helpers::resposta_json(true, 'Bloqueio criado com sucesso.', ['id' => $id], 201);
    }

    if ($action === 'bloqueio_editar') {
        $id         = (int) ($body['id'] ?? 0);
        $diaSemana  = isset($body['dia_semana']) && $body['dia_semana'] !== '' && $body['dia_semana'] !== null
            ? (int) $body['dia_semana'] : null;
        $horaInicio = trim((string) ($body['hora_inicio'] ?? ''));
        $horaFim    = trim((string) ($body['hora_fim']    ?? ''));
        $descricao  = trim((string) ($body['descricao']   ?? '')) ?: null;

        if ($id <= 0) helpers::resposta_json(false, 'ID inválido.', null, 400);
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
        $diasExcecao = parseDiasExcecao($body, $diaSemana);
        HorariosSql::editarBloqueio($id, $diaSemana, $horaInicio, $horaFim, $descricao, $diasExcecao);
        helpers::resposta_json(true, 'Bloqueio atualizado.', ['id' => $id]);
    }

    if ($action === 'bloqueio_excluir') {
        $id = (int) ($body['id'] ?? 0);
        if ($id <= 0) helpers::resposta_json(false, 'ID inválido.', null, 400);
        HorariosSql::excluirBloqueio($id);
        helpers::resposta_json(true, 'Bloqueio removido.');
    }

    // ── Períodos (bloqueios por intervalo de datas — ex.: férias) ────────
    if ($action === 'periodo_criar' || $action === 'periodo_editar') {
        $id         = (int) ($body['id'] ?? 0);
        $dataInicio = trim((string) ($body['data_inicio'] ?? ''));
        $dataFim    = trim((string) ($body['data_fim']    ?? ''));
        $diaInteiro = !empty($body['dia_inteiro']);
        $descricao  = trim((string) ($body['descricao']   ?? '')) ?: null;

        $reData = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($reData, $dataInicio) || !preg_match($reData, $dataFim)) {
            helpers::resposta_json(false, 'Informe datas válidas.', null, 400);
        }
        if ($dataFim < $dataInicio) {
            helpers::resposta_json(false, 'A data final deve ser igual ou posterior à inicial.', null, 400);
        }

        $horaInicio = null;
        $horaFim    = null;
        if (!$diaInteiro) {
            $horaInicio = trim((string) ($body['hora_inicio'] ?? ''));
            $horaFim    = trim((string) ($body['hora_fim']    ?? ''));
            $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
            if (!preg_match($reHora, $horaInicio) || !preg_match($reHora, $horaFim)) {
                helpers::resposta_json(false, 'Informe horários válidos (HH:MM) ou marque "dia inteiro".', null, 400);
            }
            if (strlen($horaInicio) === 5) $horaInicio .= ':00';
            if (strlen($horaFim)    === 5) $horaFim    .= ':00';
            if ($horaInicio >= $horaFim) {
                helpers::resposta_json(false, 'O início deve ser anterior ao fim.', null, 400);
            }
        }
        if ($descricao !== null && mb_strlen($descricao) > 150) {
            $descricao = mb_substr($descricao, 0, 150);
        }

        try {
            if ($action === 'periodo_editar') {
                if ($id <= 0) helpers::resposta_json(false, 'ID inválido.', null, 400);
                HorariosSql::editarPeriodo($id, $dataInicio, $dataFim, $horaInicio, $horaFim, $descricao);
                helpers::resposta_json(true, 'Período atualizado com sucesso.', ['id' => $id]);
            }
            $novoId = HorariosSql::criarPeriodo($dataInicio, $dataFim, $horaInicio, $horaFim, $descricao);
        } catch (PDOException $e) {
            helpers::resposta_json(false, 'Tabela de períodos não encontrada. Execute a migração 001_horarios_periodos.sql.', null, 500);
        }
        helpers::resposta_json(true, 'Período criado com sucesso.', ['id' => $novoId], 201);
    }

    if ($action === 'periodo_excluir') {
        $id = (int) ($body['id'] ?? 0);
        if ($id <= 0) helpers::resposta_json(false, 'ID inválido.', null, 400);
        HorariosSql::excluirPeriodo($id);
        helpers::resposta_json(true, 'Período removido.');
    }

    // ── Salvar todos os dias de uma vez ──────────────────────────
    if ($action === 'salvar_todos') {
        $dias = is_array($body['dias'] ?? null) ? $body['dias'] : [];
        if (empty($dias)) {
            helpers::resposta_json(false, 'Nenhum dia informado.', null, 400);
        }
        $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
        $erros  = [];
        foreach ($dias as $diaData) {
            $ds  = (int) ($diaData['dia_semana'] ?? 0);
            $ab  = trim((string) ($diaData['abertura']   ?? ''));
            $fe  = trim((string) ($diaData['fechamento'] ?? ''));
            $fec = !empty($diaData['fechado']);
            if ($ds < 1 || $ds > 7) {
                $erros[] = "Dia inv\u00e1lido: $ds";
                continue;
            }
            if (!$fec && (!preg_match($reHora, $ab) || !preg_match($reHora, $fe))) {
                $erros[] = "Hor\u00e1rio inv\u00e1lido no dia $ds";
                continue;
            }
            $ab  = $fec ? '08:00:00' : (strlen($ab) === 5 ? $ab . ':00' : $ab);
            $fe  = $fec ? '20:00:00' : (strlen($fe) === 5 ? $fe . ':00' : $fe);
            if (!$fec && $ab >= $fe) {
                $erros[] = "Abertura >= fechamento no dia $ds";
                continue;
            }
            try {
                HorariosSql::salvar($ds, $ab, $fe, $fec);
            } catch (Throwable $e) {
                $erros[] = "Erro ao salvar dia $ds: " . $e->getMessage();
            }
        }
        if (!empty($erros)) {
            helpers::resposta_json(false, 'Alguns dias n\u00e3o foram salvos.', $erros, 422);
        }
        helpers::resposta_json(true, 'Todos os hor\u00e1rios salvos com sucesso.');
    }

    // ── Salvar horário de um dia ────────────────────────────────
    if ($action === 'salvar') {
        $diaSemana  = (int) ($body['dia_semana'] ?? 0);
        $abertura   = trim((string) ($body['abertura']   ?? ''));
        $fechamento = trim((string) ($body['fechamento'] ?? ''));
        $fechado    = !empty($body['fechado']);

        if ($diaSemana < 1 || $diaSemana > 7) {
            helpers::resposta_json(false, 'Dia da semana inv\u00e1lido (1=Seg \u2026 7=Dom).', null, 400);
        }
        $reHora = '/^\d{2}:\d{2}(:\d{2})?$/';
        if (!$fechado && (!preg_match($reHora, $abertura) || !preg_match($reHora, $fechamento))) {
            helpers::resposta_json(false, 'Informe hor\u00e1rios v\u00e1lidos no formato HH:MM.', null, 400);
        }
        $abertura   = $fechado ? '08:00:00' : (strlen($abertura)   === 5 ? $abertura   . ':00' : $abertura);
        $fechamento = $fechado ? '20:00:00' : (strlen($fechamento) === 5 ? $fechamento . ':00' : $fechamento);
        if (!$fechado && $abertura >= $fechamento) {
            helpers::resposta_json(false, 'O hor\u00e1rio de abertura deve ser anterior ao de fechamento.', null, 400);
        }
        try {
            HorariosSql::salvar($diaSemana, $abertura, $fechamento, $fechado);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            helpers::resposta_json(false, 'N\u00e3o foi poss\u00edvel salvar. Verifique se o schema SQL foi executado.', null, 500);
        }
        helpers::resposta_json(true, 'Hor\u00e1rio salvo com sucesso.');
    }

    helpers::resposta_json(false, 'A\u00e7\u00e3o n\u00e3o reconhecida.', null, 400);
}

helpers::resposta_json(false, 'Método não permitido.', null, 405);

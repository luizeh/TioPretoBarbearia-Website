<?php

/**
 * api/user/agendamentos.php
 * Agendamentos do usuário logado.
 * GET           → lista agendamentos do usuário
 * POST action=criar   → cria novo agendamento
 * POST action=cancelar → cancela agendamento do usuário
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

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$method    = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $agendamentos = AgendamentosSql::listarPorUsuario($usuarioId);
    helpers::resposta_json(true, 'OK', $agendamentos, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        $required = ['servico_id', 'data', 'hora_inicio', 'hora_fim'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        $body['usuario_id'] = $usuarioId;
        $body['status']     = 'pendente';
        $id = AgendamentosSql::criar($body);

        // ── Envio automático de WhatsApp de confirmação ──────────
        try {
            $pdo      = Connection::getConnection();
            $stmtU    = $pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id = :id");
            $stmtU->execute([':id' => $usuarioId]);
            $usuario  = $stmtU->fetch();

            $stmtS    = $pdo->prepare("SELECT nome FROM servicos WHERE id = :id");
            $stmtS->execute([':id' => (int) $body['servico_id']]);
            $servico  = $stmtS->fetch();

            if ($usuario && !empty($usuario['telefone'])) {
                $tel      = preg_replace('/\D/', '', $usuario['telefone']);
                if (!str_starts_with($tel, '55')) $tel = '55' . $tel;
                $dataBr   = date('d/m/Y', strtotime($body['data']));
                $hora     = substr($body['hora_inicio'], 0, 5);
                $nomeSvc  = $servico['nome'] ?? 'serviço';
                $msg      = "Olá, {$usuario['nome']}! ✅ Seu agendamento foi confirmado!\n\n"
                    . "📋 *Serviço:* {$nomeSvc}\n"
                    . "📅 *Data:* {$dataBr}\n"
                    . "⏰ *Horário:* {$hora}\n\n"
                    . "Te esperamos na *Tio Preto Barbearia*! 💈";

                $ch = curl_init('https://dev-api.r4dev.com.br/v1/instance/cmqqzc2j1002d104shfslo3sj/messages/chat');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_HTTPHEADER     => ['Token: cmqqzc2j2002e104so1o09hqy', 'Content-Type: application/json'],
                    CURLOPT_POSTFIELDS     => json_encode(['to' => $tel, 'body' => $msg], JSON_UNESCAPED_UNICODE),
                    CURLOPT_TIMEOUT        => 8,
                ]);
                curl_exec($ch);
                curl_close($ch);
            }
        } catch (\Throwable $e) {
            // WhatsApp silencioso — não bloqueia a criação do agendamento
        }

        helpers::resposta_json(true, 'Agendamento criado com sucesso!', ['id' => $id], 201);
    }

    if ($action === 'cancelar') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID do agendamento é obrigatório.', null, 400);
        }
        // Verifica se pertence ao usuário
        $ag = AgendamentosSql::buscarPorId((int) $body['id']);
        if (!$ag || (int) $ag['usuario_id'] !== $usuarioId) {
            helpers::resposta_json(false, 'Agendamento não encontrado.', null, 404);
        }
        AgendamentosSql::editarStatus((int) $body['id'], 'cancelado');
        helpers::resposta_json(true, 'Agendamento cancelado com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);

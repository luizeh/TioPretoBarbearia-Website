<?php

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/LogsSql.php';
require_once __DIR__ . '/../../sql/AgendamentosSql.php';

function enviarMensagemWhatsApp(string $telefone, string $mensagem): array
{
    $telefone = preg_replace('/\D/', '', $telefone);
    if ($telefone === '') {
        return ['success' => false, 'message' => 'Telefone nao informado.'];
    }
    if (!str_starts_with($telefone, '55')) {
        $telefone = '55' . $telefone;
    }
    if (strlen($telefone) < 12 || strlen($telefone) > 13) {
        return ['success' => false, 'message' => 'Numero de telefone invalido.'];
    }

    $ch = curl_init('https://dev-api.r4dev.com.br/v1/instance/cmqqzc2j1002d104shfslo3sj/messages/chat');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Token: cmqqzc2j2002e104so1o09hqy',
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode(['to' => $telefone, 'body' => $mensagem], JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError) {
        return ['success' => false, 'message' => 'Erro de conexao com a API de mensagens.'];
    }

    $decoded = json_decode($response, true);
    if ($httpCode >= 200 && $httpCode < 300 && !empty($decoded['id'])) {
        return ['success' => true, 'id' => $decoded['id'], 'telefone' => $telefone];
    }

    return ['success' => false, 'message' => 'A API de mensagens retornou um erro.', 'http_code' => $httpCode];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    helpers::resposta_json(false, 'Metodo nao permitido.', null, 405);
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? 'individual';

if ($action === 'enviar_dia') {
    $data = trim($body['data'] ?? '');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        helpers::resposta_json(false, 'Informe uma data valida.', null, 400);
    }

    $enviados = 0;
    $semTelefone = 0;
    $falhas = 0;
    foreach (AgendamentosSql::listarPorPeriodo($data, $data) as $agendamento) {
        if (($agendamento['status'] ?? '') === 'cancelado') {
            continue;
        }
        if (empty($agendamento['telefone'])) {
            $semTelefone++;
            continue;
        }

        $mensagem = 'Olá, ' . $agendamento['cliente'] . "! 👋\n\n"
            . "Lembramos que você tem um agendamento na *Tio Preto Barbearia*:\n\n"
            . '📋 *Serviço:* ' . $agendamento['servico'] . "\n"
            . '📅 *Data:* ' . $agendamento['data_fmt'] . "\n"
            . '⏰ *Horário:* ' . substr($agendamento['hora_inicio'], 0, 5) . "\n\n"
            . "Aguardamos você! 💈\nTio Preto Barbearia 🧔🏿";
        $resultado = enviarMensagemWhatsApp($agendamento['telefone'], $mensagem);
        if ($resultado['success']) {
            $enviados++;
            LogsSql::registrar((int) $_SESSION['usuario_id'], 'whatsapp_enviado', 'Lembrete do dia enviado para o telefone final ' . substr($resultado['telefone'], -4) . '.');
        } else {
            $falhas++;
        }
    }

    helpers::resposta_json(true, 'Processamento dos lembretes concluido.', [
        'enviados' => $enviados,
        'sem_telefone' => $semTelefone,
        'falhas' => $falhas,
    ]);
}

$telefone = trim($body['telefone'] ?? '');
$mensagem = trim($body['mensagem'] ?? '');
if ($telefone === '' || $mensagem === '') {
    helpers::resposta_json(false, 'Telefone e mensagem sao obrigatorios.', null, 400);
}

$resultado = enviarMensagemWhatsApp($telefone, $mensagem);
if (!$resultado['success']) {
    helpers::resposta_json(false, $resultado['message'], null, 502);
}

LogsSql::registrar((int) $_SESSION['usuario_id'], 'whatsapp_enviado', 'Mensagem enviada para o telefone final ' . substr($resultado['telefone'], -4) . '.');
helpers::resposta_json(true, 'Lembrete enviado com sucesso.', ['whatsapp_id' => $resultado['id']], 200);

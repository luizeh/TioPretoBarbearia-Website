<?php

/**
 * api/admin/whatsapp.php
 * Envia uma mensagem WhatsApp para o cliente via API r4dev.
 *
 * POST body (JSON):
 *   telefone  string  — número com DDI (ex: 5544999990000)
 *   mensagem  string  — texto da mensagem
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../helpers/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    helpers::resposta_json(false, 'Método não permitido.', null, 405);
}

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$telefone = trim($body['telefone'] ?? '');
$mensagem = trim($body['mensagem'] ?? '');

if (empty($telefone) || empty($mensagem)) {
    helpers::resposta_json(false, 'Telefone e mensagem são obrigatórios.', null, 400);
}

// Garante apenas dígitos e que começa com 55 (Brasil)
$telefone = preg_replace('/\D/', '', $telefone);
if (strlen($telefone) < 10) {
    helpers::resposta_json(false, 'Número de telefone inválido.', null, 400);
}
if (!str_starts_with($telefone, '55')) {
    $telefone = '55' . $telefone;
}

$apiUrl = 'https://dev-api.r4dev.com.br/v1/instance/cmqqzc2j1002d104shfslo3sj/messages/chat';
$token  = 'cmqqzc2j2002e104so1o09hqy';

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Token: ' . $token,
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS     => json_encode([
        'to'   => $telefone,
        'body' => $mensagem,
    ], JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    helpers::resposta_json(false, 'Erro de conexão com a API de mensagens.', ['curl_error' => $curlError], 502);
}

$decoded = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300 && !empty($decoded['id'])) {
    helpers::resposta_json(true, 'Lembrete enviado com sucesso.', ['whatsapp_id' => $decoded['id']], 200);
}

helpers::resposta_json(false, 'A API de mensagens retornou um erro.', [
    'http_code' => $httpCode,
    'response'  => $decoded,
], 502);

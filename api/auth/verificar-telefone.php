<?php

/**
 * api/auth/verificar-telefone.php
 * POST action=verificar → { codigo }  → valida o código e marca o telefone verificado
 * POST action=reenviar  →              → gera e envia um novo código via WhatsApp
 * GET                    →              → status de reenvio (segundos de espera)
 *
 * O usuário-alvo vem SEMPRE de $_SESSION['pendente_verificacao']. Nunca do cliente.
 * A conta só é considerada ativa quando e-mail E telefone estão verificados.
 */

ob_start();
error_reporting(0);

include_once __DIR__ . '/../../helpers/helpers.php';
include_once __DIR__ . '/../../sql/VerificacaoSql.php';
include_once __DIR__ . '/../../sql/UsuariosSql.php';
include_once __DIR__ . '/../../sql/LogsSql.php';
include_once __DIR__ . '/../../helpers/Whatsapp.php';

helpers::iniciarSessao();

$pendente = $_SESSION['pendente_verificacao'] ?? null;
if (empty($pendente['usuario_id'])) {
    helpers::resposta_json(false, 'Nenhuma verificação pendente. Faça login ou cadastre-se.', ['redirect' => 'login.php'], 401);
}

$usuarioId = (int) $pendente['usuario_id'];
$method    = $_SERVER['REQUEST_METHOD'];

// O telefone-alvo é sempre lido do banco (fonte da verdade), com fallback na sessão.
$status   = UsuariosSql::statusVerificacao($usuarioId);
$telefone = ($status['telefone'] ?? '') ?: ($pendente['telefone'] ?? '');

try {
    if ($method === 'GET') {
        helpers::resposta_json(true, 'OK', VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::TELEFONE));
    }

    if ($method !== 'POST') {
        helpers::resposta_json(false, 'Método não reconhecido.', null, 405);
    }

    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    helpers::verificarCsrf($body);

    if ($action === 'verificar') {
        $resultado = VerificacaoSql::validar($usuarioId, VerificacaoSql::TELEFONE, $body['codigo'] ?? '');

        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }

        UsuariosSql::marcarTelefoneVerificado($usuarioId);
        LogsSql::registrar($usuarioId, 'telefone_verificado', 'Telefone confirmado por código (WhatsApp).');
        unset($_SESSION['pendente_verificacao']);

        helpers::resposta_json(true, 'Telefone verificado! Sua conta está ativa. Faça login para continuar.', ['redirect' => 'login.php'], 200);
    }

    if ($action === 'reenviar') {
        if ($telefone === '') {
            helpers::resposta_json(false, 'Não há telefone para verificar. Faça login novamente.', ['redirect' => 'login.php'], 400);
        }

        $statusReenvio = VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::TELEFONE);
        if (!$statusReenvio['pode']) {
            helpers::resposta_json(false, $statusReenvio['message'], ['espera' => $statusReenvio['espera']], 429);
        }

        $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::TELEFONE, 'whatsapp', $telefone);
        try {
            Whatsapp::enviarCodigo($telefone, $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            error_log('Reenvio telefone: falha ao enviar código.');
            helpers::resposta_json(false, 'Não foi possível enviar o WhatsApp agora. Tente novamente em instantes.', null, 502);
        }

        LogsSql::registrar($usuarioId, 'codigo_reenviado', 'Novo código de verificação de telefone enviado (WhatsApp).');
        helpers::resposta_json(true, 'Enviamos um novo código para o seu WhatsApp.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
} catch (Throwable $e) {
    error_log('verificar-telefone: ' . $e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir a verificação.', null, 500);
}

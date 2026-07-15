<?php

/**
 * api/auth/verificar-email.php
 * POST action=verificar → { codigo }  → valida o código e libera a conta
 * POST action=reenviar  →              → gera e envia um novo código
 * GET                    →              → status de reenvio (segundos de espera)
 *
 * O usuário-alvo vem SEMPRE de $_SESSION['pendente_verificacao'] (definido no
 * cadastro ou ao tentar logar com conta não verificada). Nunca do cliente.
 */

ob_start();
error_reporting(0);

include_once __DIR__ . '/../../helpers/helpers.php';
include_once __DIR__ . '/../../sql/VerificacaoSql.php';
include_once __DIR__ . '/../../sql/UsuariosSql.php';
include_once __DIR__ . '/../../sql/LogsSql.php';
include_once __DIR__ . '/../../helpers/Mailer.php';

helpers::iniciarSessao();

$pendente = $_SESSION['pendente_verificacao'] ?? null;
if (empty($pendente['usuario_id'])) {
    helpers::resposta_json(false, 'Nenhuma verificação pendente. Faça login ou cadastre-se.', ['redirect' => 'login.php'], 401);
}

$usuarioId = (int) $pendente['usuario_id'];
$email     = $pendente['email'] ?? '';
$nome      = $pendente['nome'] ?? '';
$method    = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        helpers::resposta_json(true, 'OK', VerificacaoSql::statusReenvio($usuarioId));
    }

    if ($method !== 'POST') {
        helpers::resposta_json(false, 'Método não reconhecido.', null, 405);
    }

    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    helpers::verificarCsrf($body);

    if ($action === 'verificar') {
        $resultado = VerificacaoSql::verificar($usuarioId, $body['codigo'] ?? '');

        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }

        LogsSql::registrar($usuarioId, 'email_verificado', 'E-mail confirmado por código.');
        unset($_SESSION['pendente_verificacao']);

        helpers::resposta_json(true, 'E-mail verificado! Faça login para continuar.', ['redirect' => 'login.php'], 200);
    }

    if ($action === 'reenviar') {
        $status = VerificacaoSql::statusReenvio($usuarioId);
        if (!$status['pode']) {
            helpers::resposta_json(false, $status['message'], ['espera' => $status['espera']], 429);
        }

        $codigo = VerificacaoSql::gerarParaUsuario($usuarioId, $email);
        try {
            Mailer::enviarCodigoVerificacao($email, $nome, $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            error_log('Reenvio: falha ao enviar código — ' . $e->getMessage());
            helpers::resposta_json(false, 'Não foi possível enviar o e-mail agora. Tente novamente em instantes.', null, 502);
        }

        LogsSql::registrar($usuarioId, 'codigo_reenviado', 'Novo código de verificação enviado.');
        helpers::resposta_json(true, 'Enviamos um novo código para o seu e-mail.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
} catch (Throwable $e) {
    error_log('verificar-email: ' . $e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir a verificação.', null, 500);
}

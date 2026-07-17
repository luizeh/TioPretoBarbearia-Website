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
include_once __DIR__ . '/../../helpers/CadastroPendente.php';
include_once __DIR__ . '/../../helpers/Whatsapp.php';

helpers::iniciarSessao();

// ── Cadastro em duas etapas: ao confirmar o telefone (com o e-mail já
// confirmado), a conta é FINALMENTE criada no banco. Antes disso nada é gravado.
if (CadastroPendente::existe()) {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            helpers::resposta_json(true, 'OK', CadastroPendente::statusReenvio(CadastroPendente::TELEFONE));
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            helpers::resposta_json(false, 'Método não reconhecido.', null, 405);
        }

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';
        helpers::verificarCsrf($body);
        $dados = CadastroPendente::dados();

        if ($action === 'verificar') {
            // O telefone só é confirmado após o e-mail (ordem do fluxo).
            if (!CadastroPendente::verificado(CadastroPendente::EMAIL)) {
                helpers::resposta_json(false, 'Confirme primeiro o seu e-mail.', ['redirect' => 'verificar-email.php'], 400);
            }

            $resultado = CadastroPendente::validar(CadastroPendente::TELEFONE, $body['codigo'] ?? '');
            if (!$resultado['success']) {
                helpers::resposta_json(false, $resultado['message'], null, 400);
            }

            // E-mail E telefone confirmados → cria a conta AGORA (verificada).
            $criacao = UsuariosSql::cadastrarVerificado($dados);
            if (!$criacao['success']) {
                CadastroPendente::limpar();
                helpers::resposta_json(false, $criacao['message'] . ' Refaça o cadastro.', ['redirect' => 'cadastro.php'], 409);
            }

            LogsSql::registrar((int) $criacao['id'], 'conta_criada', 'Conta de cliente criada após verificação de e-mail e telefone.');
            CadastroPendente::limpar();

            helpers::resposta_json(true, 'Telefone verificado! Sua conta está ativa. Faça login para continuar.', ['redirect' => 'login.php'], 200);
        }

        if ($action === 'reenviar') {
            if (!CadastroPendente::verificado(CadastroPendente::EMAIL)) {
                helpers::resposta_json(false, 'Confirme primeiro o seu e-mail.', ['redirect' => 'verificar-email.php'], 400);
            }
            $status = CadastroPendente::statusReenvio(CadastroPendente::TELEFONE);
            if (!$status['pode']) {
                helpers::resposta_json(false, $status['message'], ['espera' => $status['espera']], 429);
            }
            $codigo = CadastroPendente::gerarCodigo(CadastroPendente::TELEFONE);
            try {
                Whatsapp::enviarCodigo($dados['telefone'], $codigo, CadastroPendente::VALIDADE_MIN);
            } catch (RuntimeException $e) {
                error_log('Reenvio telefone (cadastro): falha ao enviar código.');
                helpers::resposta_json(false, 'Não foi possível enviar o WhatsApp agora. Tente novamente em instantes.', null, 502);
            }
            helpers::resposta_json(true, 'Enviamos um novo código para o seu WhatsApp.', ['espera' => CadastroPendente::COOLDOWN_SEG], 200);
        }

        helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
    } catch (Throwable $e) {
        error_log('verificar-telefone (cadastro): ' . $e->getMessage());
        helpers::resposta_json(false, 'Não foi possível concluir a verificação.', null, 500);
    }
}

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

<?php

/**
 * api/auth/verificar-email.php
 * POST action=verificar → { codigo }  → valida o código e marca o e-mail verificado
 * POST action=reenviar  →              → gera e envia um novo código
 * GET                    →              → status de reenvio (segundos de espera)
 *
 * O usuário-alvo vem SEMPRE de $_SESSION['pendente_verificacao'] (definido no
 * cadastro ou ao tentar logar com conta não verificada). Nunca do cliente.
 *
 * Após confirmar o e-mail, se o telefone ainda não estiver verificado, o fluxo
 * segue para a verificação de telefone (a conta só fica ativa com as duas).
 */

ob_start();
error_reporting(0);

include_once __DIR__ . '/../../helpers/helpers.php';
include_once __DIR__ . '/../../sql/VerificacaoSql.php';
include_once __DIR__ . '/../../sql/UsuariosSql.php';
include_once __DIR__ . '/../../sql/LogsSql.php';
include_once __DIR__ . '/../../helpers/CadastroPendente.php';
include_once __DIR__ . '/../../helpers/Mailer.php';
include_once __DIR__ . '/../../helpers/Whatsapp.php';

helpers::iniciarSessao();

// ── Cadastro em duas etapas: a conta AINDA NÃO existe no banco (fica na sessão).
// Confirma o e-mail e, em seguida, dispara o código de telefone.
if (CadastroPendente::existe()) {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            helpers::resposta_json(true, 'OK', CadastroPendente::statusReenvio(CadastroPendente::EMAIL));
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            helpers::resposta_json(false, 'Método não reconhecido.', null, 405);
        }

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';
        helpers::verificarCsrf($body);
        $dados = CadastroPendente::dados();

        if ($action === 'verificar') {
            $resultado = CadastroPendente::validar(CadastroPendente::EMAIL, $body['codigo'] ?? '');
            if (!$resultado['success']) {
                helpers::resposta_json(false, $resultado['message'], null, 400);
            }

            // E-mail confirmado. Dispara o código de telefone (WhatsApp) se ainda pendente.
            if (!CadastroPendente::verificado(CadastroPendente::TELEFONE)
                && CadastroPendente::statusReenvio(CadastroPendente::TELEFONE)['pode']) {
                try {
                    $codigo = CadastroPendente::gerarCodigo(CadastroPendente::TELEFONE);
                    Whatsapp::enviarCodigo($dados['telefone'], $codigo, CadastroPendente::VALIDADE_MIN);
                } catch (RuntimeException $e) {
                    error_log('verificar-email (cadastro): falha ao enviar código de telefone.');
                }
            }
            helpers::resposta_json(true, 'E-mail verificado! Agora confirme seu telefone.', ['redirect' => 'verificar-telefone.php'], 200);
        }

        if ($action === 'reenviar') {
            $status = CadastroPendente::statusReenvio(CadastroPendente::EMAIL);
            if (!$status['pode']) {
                helpers::resposta_json(false, $status['message'], ['espera' => $status['espera']], 429);
            }
            $codigo = CadastroPendente::gerarCodigo(CadastroPendente::EMAIL);
            try {
                Mailer::enviarCodigoVerificacao($dados['email'], $dados['nome'], $codigo, CadastroPendente::VALIDADE_MIN);
            } catch (RuntimeException $e) {
                error_log('Reenvio e-mail (cadastro): falha ao enviar código.');
                helpers::resposta_json(false, 'Não foi possível enviar o e-mail agora. Tente novamente em instantes.', null, 502);
            }
            helpers::resposta_json(true, 'Enviamos um novo código para o seu e-mail.', ['espera' => CadastroPendente::COOLDOWN_SEG], 200);
        }

        helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
    } catch (Throwable $e) {
        error_log('verificar-email (cadastro): ' . $e->getMessage());
        helpers::resposta_json(false, 'Não foi possível concluir a verificação.', null, 500);
    }
}

$pendente = $_SESSION['pendente_verificacao'] ?? null;
if (empty($pendente['usuario_id'])) {
    helpers::resposta_json(false, 'Nenhuma verificação pendente. Faça login ou cadastre-se.', ['redirect' => 'login.php'], 401);
}

$usuarioId = (int) $pendente['usuario_id'];
$email     = $pendente['email'] ?? '';
$nome      = $pendente['nome'] ?? '';
$telefone  = $pendente['telefone'] ?? '';
$method    = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        helpers::resposta_json(true, 'OK', VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::EMAIL));
    }

    if ($method !== 'POST') {
        helpers::resposta_json(false, 'Método não reconhecido.', null, 405);
    }

    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    helpers::verificarCsrf($body);

    if ($action === 'verificar') {
        $resultado = VerificacaoSql::validar($usuarioId, VerificacaoSql::EMAIL, $body['codigo'] ?? '');

        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }

        UsuariosSql::marcarEmailVerificado($usuarioId);
        LogsSql::registrar($usuarioId, 'email_verificado', 'E-mail confirmado por código.');

        // Verifica se o telefone ainda está pendente para encadear o próximo passo.
        $status = UsuariosSql::statusVerificacao($usuarioId);
        if ($status && (int) $status['telefone_verificado'] === 0) {
            // Dispara o código de telefone (respeitando o cooldown) e segue o fluxo.
            $telefoneAlvo = $telefone !== '' ? $telefone : (string) ($status['telefone'] ?? '');
            if ($telefoneAlvo !== '' && VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::TELEFONE)['pode']) {
                try {
                    $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::TELEFONE, 'whatsapp', $telefoneAlvo);
                    Whatsapp::enviarCodigo($telefoneAlvo, $codigo, VerificacaoSql::VALIDADE_MIN);
                } catch (RuntimeException $e) {
                    error_log('verificar-email: falha ao enviar código de telefone.');
                }
            }
            helpers::resposta_json(true, 'E-mail verificado! Agora confirme seu telefone.', ['redirect' => 'verificar-telefone.php'], 200);
        }

        // E-mail e telefone verificados → conta ativa.
        unset($_SESSION['pendente_verificacao']);
        helpers::resposta_json(true, 'E-mail verificado! Faça login para continuar.', ['redirect' => 'login.php'], 200);
    }

    if ($action === 'reenviar') {
        $status = VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::EMAIL);
        if (!$status['pode']) {
            helpers::resposta_json(false, $status['message'], ['espera' => $status['espera']], 429);
        }

        $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::EMAIL, 'email', $email);
        try {
            Mailer::enviarCodigoVerificacao($email, $nome, $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            error_log('Reenvio e-mail: falha ao enviar código.');
            helpers::resposta_json(false, 'Não foi possível enviar o e-mail agora. Tente novamente em instantes.', null, 502);
        }

        LogsSql::registrar($usuarioId, 'codigo_reenviado', 'Novo código de verificação de e-mail enviado.');
        helpers::resposta_json(true, 'Enviamos um novo código para o seu e-mail.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
} catch (Throwable $e) {
    error_log('verificar-email: ' . $e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir a verificação.', null, 500);
}

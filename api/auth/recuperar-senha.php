<?php

/**
 * api/auth/recuperar-senha.php  — "Esqueci minha senha"
 *
 * Fluxo (todas as etapas por POST + CSRF):
 *   action=solicitar → { canal: 'email'|'whatsapp', identificador } → envia um código
 *   action=validar   → { codigo }                                   → autoriza a troca
 *   action=redefinir → { nova_senha, confirmar_senha }              → grava a nova senha
 *   action=reenviar  →                                              → reenvia o código
 *
 * Regras de segurança:
 *   - Recuperação apenas por e-mail verificado OU telefone verificado (WhatsApp).
 *   - Mensagens genéricas: nunca revelam se o e-mail/telefone existe.
 *   - Códigos de 6 dígitos, hash, expiração, limite de tentativas e reenvios.
 *   - Códigos anteriores são invalidados a cada envio e após a redefinição.
 *   - O contexto (usuário-alvo) fica só na sessão do servidor — nunca no cliente.
 */

ob_start();
error_reporting(0);

include_once __DIR__ . '/../../helpers/helpers.php';
include_once __DIR__ . '/../../sql/VerificacaoSql.php';
include_once __DIR__ . '/../../sql/UsuariosSql.php';
include_once __DIR__ . '/../../sql/LogsSql.php';
include_once __DIR__ . '/../../helpers/Mailer.php';
include_once __DIR__ . '/../../helpers/Whatsapp.php';

helpers::iniciarSessao();

$MSG_GENERICO = 'Se os dados informados estiverem corretos, você receberá um código com as instruções.';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    helpers::resposta_json(false, 'Método não permitido.', null, 405);
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? '';

helpers::verificarCsrf($body);

try {
    // ── 1. Solicitar código ──────────────────────────────────────────
    if ($action === 'solicitar') {
        $canal = ($body['canal'] ?? '') === 'whatsapp' ? 'whatsapp' : 'email';
        $ident = trim((string) ($body['identificador'] ?? ''));

        if ($ident === '') {
            helpers::resposta_json(false, 'Informe seu e-mail ou telefone.', null, 400);
        }

        // Localiza o usuário conforme o canal, exigindo que o canal esteja verificado.
        $usuario = false;
        $destino = '';
        if ($canal === 'email') {
            $email   = filter_var($ident, FILTER_VALIDATE_EMAIL) ? strtolower($ident) : '';
            if ($email !== '') {
                $u = UsuariosSql::buscarPorEmail($email);
                if ($u && (int) $u['email_verificado'] === 1) {
                    $usuario = $u;
                    $destino = $email;
                }
            }
        } else {
            $telefone = preg_replace('/\D/', '', $ident);
            if ($telefone !== '' && !str_starts_with($telefone, '55')) {
                $telefone = '55' . $telefone;
            }
            if (preg_match('/^55\d{10,11}$/', (string) $telefone)) {
                $u = UsuariosSql::buscarPorTelefone($telefone);
                if ($u && (int) $u['telefone_verificado'] === 1) {
                    $usuario = $u;
                    $destino = $telefone;
                }
            }
        }

        // Só gera/envia se o usuário existe e o canal está verificado — mas a
        // resposta é sempre genérica (não revela existência).
        if ($usuario) {
            $usuarioId = (int) $usuario['id'];
            if (VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::RECUPERACAO)['pode']) {
                $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::RECUPERACAO, $canal, $destino);
                try {
                    if ($canal === 'email') {
                        Mailer::enviarCodigoRecuperacao($destino, $usuario['nome'], $codigo, VerificacaoSql::VALIDADE_MIN);
                    } else {
                        Whatsapp::enviarCodigo($destino, $codigo, VerificacaoSql::VALIDADE_MIN, 'recuperação de senha');
                    }
                    LogsSql::registrar($usuarioId, 'recuperacao_solicitada', 'Código de recuperação de senha enviado por ' . $canal . '.');
                } catch (RuntimeException $e) {
                    error_log('Recuperação: falha ao enviar código.');
                }
            }
            // Guarda o contexto no servidor para as próximas etapas.
            $_SESSION['recuperacao'] = [
                'usuario_id' => $usuarioId,
                'canal'      => $canal,
                'autorizado' => false,
            ];
        } else {
            // Não encontrado / canal não verificado: limpa qualquer contexto antigo.
            unset($_SESSION['recuperacao']);
        }

        helpers::resposta_json(true, $MSG_GENERICO, ['canal' => $canal], 200);
    }

    // ── 2. Validar código ────────────────────────────────────────────
    if ($action === 'validar') {
        $ctx = $_SESSION['recuperacao'] ?? null;
        if (empty($ctx['usuario_id'])) {
            helpers::resposta_json(false, 'Solicite um novo código para continuar.', null, 400);
        }

        $resultado = VerificacaoSql::validar((int) $ctx['usuario_id'], VerificacaoSql::RECUPERACAO, $body['codigo'] ?? '');
        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }

        // Marca a sessão como autorizada a redefinir a senha.
        $_SESSION['recuperacao']['autorizado'] = true;
        helpers::resposta_json(true, 'Código confirmado. Defina sua nova senha.', null, 200);
    }

    // ── 3. Redefinir senha ───────────────────────────────────────────
    if ($action === 'redefinir') {
        $ctx = $_SESSION['recuperacao'] ?? null;
        if (empty($ctx['usuario_id']) || empty($ctx['autorizado'])) {
            helpers::resposta_json(false, 'Sua sessão de recuperação expirou. Solicite um novo código.', null, 403);
        }

        $nova      = (string) ($body['nova_senha'] ?? '');
        $confirmar = (string) ($body['confirmar_senha'] ?? '');
        $nova      = helpers::validarSenha($nova);
        if ($nova !== trim($confirmar)) {
            helpers::resposta_json(false, 'As senhas não coincidem.', null, 400);
        }

        $usuarioId = (int) $ctx['usuario_id'];
        UsuariosSql::redefinirSenha($usuarioId, $nova);
        // Invalida quaisquer códigos de recuperação remanescentes.
        VerificacaoSql::invalidarProposito($usuarioId, VerificacaoSql::RECUPERACAO);
        LogsSql::registrar($usuarioId, 'senha_redefinida', 'Senha redefinida via recuperação de conta.');

        unset($_SESSION['recuperacao']);
        helpers::resposta_json(true, 'Senha redefinida com sucesso! Faça login com a nova senha.', ['redirect' => 'login.php'], 200);
    }

    // ── Reenviar código ──────────────────────────────────────────────
    if ($action === 'reenviar') {
        $ctx = $_SESSION['recuperacao'] ?? null;
        if (empty($ctx['usuario_id'])) {
            helpers::resposta_json(false, 'Solicite um novo código para continuar.', null, 400);
        }

        $usuarioId = (int) $ctx['usuario_id'];
        $canal     = $ctx['canal'] ?? 'email';

        $statusReenvio = VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::RECUPERACAO);
        if (!$statusReenvio['pode']) {
            helpers::resposta_json(false, $statusReenvio['message'], ['espera' => $statusReenvio['espera']], 429);
        }

        $usuario = UsuariosSql::buscarPorId($usuarioId);
        if (!$usuario) {
            helpers::resposta_json(false, 'Solicite um novo código para continuar.', null, 400);
        }
        $destino = $canal === 'email' ? $usuario['email'] : $usuario['telefone'];

        $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::RECUPERACAO, $canal, $destino);
        try {
            if ($canal === 'email') {
                Mailer::enviarCodigoRecuperacao($destino, $usuario['nome'], $codigo, VerificacaoSql::VALIDADE_MIN);
            } else {
                Whatsapp::enviarCodigo($destino, $codigo, VerificacaoSql::VALIDADE_MIN, 'recuperação de senha');
            }
        } catch (RuntimeException $e) {
            error_log('Recuperação (reenvio): falha ao enviar código.');
            helpers::resposta_json(false, 'Não foi possível reenviar o código agora. Tente novamente em instantes.', null, 502);
        }

        helpers::resposta_json(true, 'Enviamos um novo código.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    helpers::resposta_json(false, 'Ação não reconhecida.', null, 400);
} catch (Throwable $e) {
    error_log('recuperar-senha: ' . $e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir a operação.', null, 500);
}

<?php

/**
 * api/user/perfil.php
 * GET  → dados do perfil + estado de verificação e pendências
 * POST action=editar             → nome, sobrenome, cidade (aplicação imediata)
 * POST action=senha              → altera a senha (exige a senha atual)
 * POST action=excluir            → exclui a conta
 * POST action=solicitar_email    → { novo_email }    → guarda pendente + envia código
 * POST action=confirmar_email    → { codigo }        → confirma a troca de e-mail
 * POST action=reenviar_email     →                   → reenvia o código de e-mail
 * POST action=solicitar_telefone → { novo_telefone } → guarda pendente + envia WhatsApp
 * POST action=confirmar_telefone → { codigo }        → confirma a troca de telefone
 * POST action=reenviar_telefone  →                   → reenvia o código de telefone
 *
 * E-mail e telefone NÃO mudam na hora: o valor novo fica pendente e o antigo
 * continua válido até a confirmação por código.
 */

require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/UsuariosSql.php';
require_once __DIR__ . '/../../sql/VerificacaoSql.php';
require_once __DIR__ . '/../../sql/LogsSql.php';
require_once __DIR__ . '/../../helpers/Mailer.php';
require_once __DIR__ . '/../../helpers/Whatsapp.php';

helpers::iniciarSessao();
helpers::verificar_login();

$id     = (int) $_SESSION['usuario_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $usuario = UsuariosSql::buscarPorId($id);
    $status  = UsuariosSql::statusVerificacao($id);
    if ($usuario && $status) {
        $usuario['email_verificado']    = (int) $status['email_verificado'];
        $usuario['telefone_verificado'] = (int) $status['telefone_verificado'];
        $usuario['email_pendente']      = $status['email_pendente'];
        $usuario['telefone_pendente']   = $status['telefone_pendente'];
    }
    helpers::resposta_json(true, 'OK', $usuario, 200);
}

if ($method !== 'POST') {
    helpers::resposta_json(false, 'Método inválido.', null, 405);
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? '';

helpers::verificarCsrf($body);

try {
    // ── Dados básicos (imediato) ─────────────────────────────────────
    if ($action === 'editar') {
        foreach (['nome', 'sobrenome', 'cidade'] as $field) {
            if (empty(trim($body[$field] ?? ''))) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        $dados = [
            'nome'      => helpers::validarTexto($body['nome'], 'nome'),
            'sobrenome' => helpers::validarTexto($body['sobrenome'], 'sobrenome'),
            'cidade'    => helpers::validarTexto($body['cidade'], 'cidade'),
        ];
        UsuariosSql::atualizarDadosBasicos($id, $dados);
        $_SESSION['usuario_nome'] = $dados['nome'];
        helpers::resposta_json(true, 'Perfil atualizado com sucesso.', null, 200);
    }

    // ── Excluir conta ────────────────────────────────────────────────
    if ($action === 'excluir') {
        try {
            if (!UsuariosSql::excluirConta($id)) {
                helpers::resposta_json(false, 'Conta não encontrada.', null, 404);
            }
        } catch (Throwable $e) {
            error_log('perfil excluir: ' . $e->getMessage());
            helpers::resposta_json(false, 'Não foi possível excluir a conta agora.', null, 500);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        helpers::resposta_json(true, 'Conta excluída com sucesso.', null, 200);
    }

    // ── Alterar senha (exige senha atual) ────────────────────────────
    if ($action === 'senha') {
        $senhaAtual = $body['senha_atual'] ?? '';
        $novaSenha  = helpers::validarSenha($body['nova_senha'] ?? '');
        $result     = UsuariosSql::alterarSenha($id, $senhaAtual, $novaSenha);
        helpers::resposta_json($result['success'], $result['message'], null, $result['success'] ? 200 : 400);
    }

    // ── Troca de e-mail (pendente até verificar) ─────────────────────
    if ($action === 'solicitar_email') {
        $novoEmail = helpers::validarEmail($body['novo_email'] ?? '');
        $atual     = UsuariosSql::buscarPorId($id);

        if ($atual && strtolower($atual['email']) === $novoEmail) {
            helpers::resposta_json(false, 'Este já é o seu e-mail atual.', null, 400);
        }
        if (UsuariosSql::emailEmUso($novoEmail, $id)) {
            helpers::resposta_json(false, 'Este e-mail já está sendo usado por outra conta.', null, 409);
        }
        if (!VerificacaoSql::statusReenvio($id, VerificacaoSql::EMAIL)['pode']) {
            $s = VerificacaoSql::statusReenvio($id, VerificacaoSql::EMAIL);
            helpers::resposta_json(false, $s['message'], ['espera' => $s['espera']], 429);
        }

        UsuariosSql::definirEmailPendente($id, $novoEmail);
        $codigo = VerificacaoSql::gerar($id, VerificacaoSql::EMAIL, 'email', $novoEmail);
        try {
            Mailer::enviarCodigoVerificacao($novoEmail, $atual['nome'] ?? '', $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            error_log('perfil solicitar_email: falha no envio.');
            helpers::resposta_json(false, 'Não foi possível enviar o e-mail agora. Tente novamente em instantes.', null, 502);
        }
        LogsSql::registrar($id, 'email_alteracao_solicitada', 'Solicitada troca de e-mail (aguardando verificação).');
        helpers::resposta_json(true, 'Enviamos um código para o novo e-mail.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    if ($action === 'confirmar_email') {
        $resultado = VerificacaoSql::validar($id, VerificacaoSql::EMAIL, $body['codigo'] ?? '');
        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }
        $novo = UsuariosSql::confirmarEmailPendente($id);
        if ($novo === null) {
            helpers::resposta_json(false, 'Nenhuma troca de e-mail pendente.', null, 400);
        }
        LogsSql::registrar($id, 'email_alterado', 'E-mail alterado e verificado.');
        helpers::resposta_json(true, 'E-mail alterado com sucesso!', ['email' => $novo], 200);
    }

    if ($action === 'reenviar_email') {
        $atual = UsuariosSql::statusVerificacao($id);
        if (empty($atual['email_pendente'])) {
            helpers::resposta_json(false, 'Nenhuma troca de e-mail pendente.', null, 400);
        }
        $s = VerificacaoSql::statusReenvio($id, VerificacaoSql::EMAIL);
        if (!$s['pode']) {
            helpers::resposta_json(false, $s['message'], ['espera' => $s['espera']], 429);
        }
        $codigo = VerificacaoSql::gerar($id, VerificacaoSql::EMAIL, 'email', $atual['email_pendente']);
        try {
            Mailer::enviarCodigoVerificacao($atual['email_pendente'], $atual['nome'] ?? '', $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            helpers::resposta_json(false, 'Não foi possível enviar o e-mail agora.', null, 502);
        }
        helpers::resposta_json(true, 'Enviamos um novo código para o e-mail pendente.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    // ── Troca de telefone (pendente até verificar) ───────────────────
    if ($action === 'solicitar_telefone') {
        $novoTelefone = helpers::normalizarTelefone($body['novo_telefone'] ?? '');
        $atual        = UsuariosSql::buscarPorId($id);

        if ($atual && $atual['telefone'] === $novoTelefone) {
            helpers::resposta_json(false, 'Este já é o seu telefone atual.', null, 400);
        }
        if (UsuariosSql::telefoneEmUso($novoTelefone, $id)) {
            helpers::resposta_json(false, 'Este telefone já está sendo usado por outra conta.', null, 409);
        }
        $s = VerificacaoSql::statusReenvio($id, VerificacaoSql::TELEFONE);
        if (!$s['pode']) {
            helpers::resposta_json(false, $s['message'], ['espera' => $s['espera']], 429);
        }

        UsuariosSql::definirTelefonePendente($id, $novoTelefone);
        $codigo = VerificacaoSql::gerar($id, VerificacaoSql::TELEFONE, 'whatsapp', $novoTelefone);
        try {
            Whatsapp::enviarCodigo($novoTelefone, $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            error_log('perfil solicitar_telefone: falha no envio.');
            helpers::resposta_json(false, 'Não foi possível enviar o WhatsApp agora. Tente novamente em instantes.', null, 502);
        }
        LogsSql::registrar($id, 'telefone_alteracao_solicitada', 'Solicitada troca de telefone (aguardando verificação).');
        helpers::resposta_json(true, 'Enviamos um código pelo WhatsApp para o novo número.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    if ($action === 'confirmar_telefone') {
        $resultado = VerificacaoSql::validar($id, VerificacaoSql::TELEFONE, $body['codigo'] ?? '');
        if (!$resultado['success']) {
            helpers::resposta_json(false, $resultado['message'], null, 400);
        }
        $novo = UsuariosSql::confirmarTelefonePendente($id);
        if ($novo === null) {
            helpers::resposta_json(false, 'Nenhuma troca de telefone pendente.', null, 400);
        }
        LogsSql::registrar($id, 'telefone_alterado', 'Telefone alterado e verificado.');
        helpers::resposta_json(true, 'Telefone alterado com sucesso!', ['telefone' => $novo], 200);
    }

    if ($action === 'reenviar_telefone') {
        $atual = UsuariosSql::statusVerificacao($id);
        if (empty($atual['telefone_pendente'])) {
            helpers::resposta_json(false, 'Nenhuma troca de telefone pendente.', null, 400);
        }
        $s = VerificacaoSql::statusReenvio($id, VerificacaoSql::TELEFONE);
        if (!$s['pode']) {
            helpers::resposta_json(false, $s['message'], ['espera' => $s['espera']], 429);
        }
        $codigo = VerificacaoSql::gerar($id, VerificacaoSql::TELEFONE, 'whatsapp', $atual['telefone_pendente']);
        try {
            Whatsapp::enviarCodigo($atual['telefone_pendente'], $codigo, VerificacaoSql::VALIDADE_MIN);
        } catch (RuntimeException $e) {
            helpers::resposta_json(false, 'Não foi possível enviar o WhatsApp agora.', null, 502);
        }
        helpers::resposta_json(true, 'Enviamos um novo código para o telefone pendente.', ['espera' => VerificacaoSql::COOLDOWN_SEG], 200);
    }

    helpers::resposta_json(false, 'Ação inválida.', null, 400);
} catch (Throwable $e) {
    error_log('perfil: ' . $e->getMessage());
    helpers::resposta_json(false, 'Não foi possível concluir a operação.', null, 500);
}

<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../helpers/helpers.php');
include_once(__DIR__ . '/../../sql/UsuariosSql.php');
include_once(__DIR__ . '/../../sql/VerificacaoSql.php');
include_once(__DIR__ . '/../../sql/LogsSql.php');
include_once(__DIR__ . '/../../helpers/Mailer.php');

helpers::iniciarSessao();

$dados = $_POST;

if (($dados['action'] ?? '') == 'cadastro') {

    helpers::verificarCsrf();

    if (!isset($dados['nome'], $dados['sobrenome'], $dados['telefone'], $dados['cidade'], $dados['email'], $dados['senha'], $dados['confirmar_senha'])) {
        helpers::resposta_json(false, 'Dados incompletos na requisição.', null, 400);
    }

    $campos = [
        'nome'            => "O nome é obrigatório",
        'sobrenome'       => "O sobrenome é obrigatório",
        'telefone'        => "O telefone é obrigatório",
        'cidade'          => "A cidade é obrigatória",
        'email'           => "O e-mail é obrigatório",
        'senha'           => "A senha é obrigatória",
        'confirmar_senha' => "Confirmar sua senha é obrigatório",
    ];

    foreach ($campos as $campo => $mensagem) {
        if (empty(trim($dados[$campo]))) {
            helpers::resposta_json(false, $mensagem, null, 400);
        }
    }

    // Validação no servidor (revalida tudo, independentemente do front-end).
    $dados['nome']      = helpers::validarTexto($dados['nome'], 'nome');
    $dados['sobrenome'] = helpers::validarTexto($dados['sobrenome'], 'sobrenome');
    $dados['cidade']    = helpers::validarTexto($dados['cidade'], 'cidade');
    $dados['email']     = helpers::validarEmail($dados['email']); // já retorna em minúsculas
    $dados['telefone']  = helpers::validarTelefone($dados['telefone']);
    $dados['senha']     = helpers::validarSenha($dados['senha']);

    if (trim($dados['senha']) !== trim($dados['confirmar_senha'])) {
        helpers::resposta_json(false, 'As senhas não coincidem.', null, 400);
    }

    // Cria a conta (não verificada). Senha é armazenada com password_hash().
    $result = UsuariosSql::cadastrar($dados);

    if (!$result['success']) {
        helpers::resposta_json(false, $result['message'], null, 409);
    }

    $usuarioId = (int) $result['id'];
    LogsSql::registrar($usuarioId, 'conta_criada', 'Conta de cliente criada (aguardando verificação de e-mail e telefone).');

    // Gera o código de verificação (operação rápida — fica salvo no banco).
    // O ENVIO do e-mail (parte lenta) é feito depois de responder ao cliente.
    $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::EMAIL, 'email', $dados['email']);

    // Guarda o contexto para as páginas de verificação (não confia em ID vindo do cliente).
    $_SESSION['pendente_verificacao'] = [
        'usuario_id' => $usuarioId,
        'email'      => $dados['email'],
        'nome'       => $dados['nome'],
        'telefone'   => $dados['telefone'],
    ];

    // ── Responde JÁ: a operação principal (criar conta + código) está concluída.
    // O usuário é redirecionado para a verificação sem esperar o SMTP.
    helpers::responderEContinuar(
        true,
        'Enviamos um código de verificação para o seu e-mail.',
        ['redirect' => 'verificar-email.php'],
        201
    );

    // ── Pós-processamento (o cliente já recebeu a resposta): envia o e-mail.
    // Se falhar, a conta e o código já existem → o usuário reenvia na próxima tela.
    try {
        Mailer::enviarCodigoVerificacao($dados['email'], $dados['nome'], $codigo, VerificacaoSql::VALIDADE_MIN);
    } catch (Throwable $e) {
        error_log('Cadastro: falha ao enviar código de e-mail no pós-processamento.');
    }
    exit;
}

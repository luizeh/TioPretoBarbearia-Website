<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../helpers/helpers.php');
include_once(__DIR__ . '/../../sql/UsuariosSql.php');
include_once(__DIR__ . '/../../helpers/CadastroPendente.php');
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

    // NÃO grava no banco ainda: a conta só é criada quando e-mail E telefone
    // forem confirmados (ver verificar-telefone.php). Aqui apenas rejeitamos
    // e-mail/telefone já usados e guardamos o cadastro na sessão do servidor.
    if (UsuariosSql::emailExiste($dados['email'])) {
        helpers::resposta_json(false, 'E-mail já cadastrado.', null, 409);
    }
    if (UsuariosSql::telefoneExiste($dados['telefone'])) {
        helpers::resposta_json(false, 'Telefone já cadastrado.', null, 409);
    }

    // Guarda o cadastro pendente (com a senha em hash) e gera o código de e-mail.
    CadastroPendente::iniciar($dados);
    $codigo = CadastroPendente::gerarCodigo(CadastroPendente::EMAIL);

    // ── Responde JÁ: o cadastro pendente está pronto; o usuário segue para a
    // verificação sem esperar o SMTP. Nenhum registro foi criado no banco.
    helpers::responderEContinuar(
        true,
        'Enviamos um código de verificação para o seu e-mail.',
        ['redirect' => 'verificar-email.php'],
        201
    );

    // ── Pós-processamento (cliente já respondido): envia o e-mail.
    // Se falhar, o cadastro pendente e o código continuam na sessão → reenvio.
    try {
        Mailer::enviarCodigoVerificacao($dados['email'], $dados['nome'], $codigo, CadastroPendente::VALIDADE_MIN);
    } catch (Throwable $e) {
        error_log('Cadastro: falha ao enviar código de e-mail no pós-processamento.');
    }
    exit;
}

<?php
ob_start();
error_reporting(0);
include_once(__DIR__ . '/../../config/connection.php');
include_once(__DIR__ . '/../../helpers/helpers.php');
include_once(__DIR__ . '/../../sql/VerificacaoSql.php');
include_once(__DIR__ . '/../../helpers/Mailer.php');
include_once(__DIR__ . '/../../helpers/Whatsapp.php');

helpers::iniciarSessao();

// PDO com prepared statement — sem SQL injection
$pdo = Connection::getConnection();

$dados = $_POST;

if (($dados['action'] ?? '') == 'login') {

    helpers::verificarCsrf();

    $email = helpers::validarEmail($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    if (empty($senha) || empty($email)) {
        helpers::resposta_json(false, 'E-mail e senha são obrigatórios.', null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    $usuario = $stmt->fetch();

    // Mensagem genérica para não revelar se o e-mail existe.
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        helpers::resposta_json(false, 'E-mail ou senha inválidos.', null, 401);
    }

    $usuarioId = (int) $usuario['id'];
    $isAdmin   = !empty($usuario['admin']);

    // Contas não-admin precisam de e-mail E telefone verificados. Credenciais
    // válidas → segue para concluir a etapa pendente (sem logar de fato).
    if (!$isAdmin) {
        $emailOk    = (int) ($usuario['email_verificado'] ?? 0) === 1;
        $telefoneOk = (int) ($usuario['telefone_verificado'] ?? 0) === 1;

        if (!$emailOk || !$telefoneOk) {
            $_SESSION['pendente_verificacao'] = [
                'usuario_id' => $usuarioId,
                'email'      => $usuario['email'],
                'nome'       => $usuario['nome'],
                'telefone'   => $usuario['telefone'],
            ];

            if (!$emailOk) {
                // (Re)envia o código de e-mail se não houver um recente.
                if (VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::EMAIL)['pode']) {
                    try {
                        $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::EMAIL, 'email', $usuario['email']);
                        Mailer::enviarCodigoVerificacao($usuario['email'], $usuario['nome'], $codigo, VerificacaoSql::VALIDADE_MIN);
                    } catch (RuntimeException $e) {
                        error_log('Login: falha ao enviar código de e-mail.');
                    }
                }
                helpers::resposta_json(
                    false,
                    'Sua conta ainda não foi verificada. Confirme seu e-mail para continuar.',
                    ['redirect' => 'verificar-email.php', 'nao_verificado' => true],
                    403
                );
            }

            // E-mail ok, telefone pendente.
            if (!empty($usuario['telefone']) && VerificacaoSql::statusReenvio($usuarioId, VerificacaoSql::TELEFONE)['pode']) {
                try {
                    $codigo = VerificacaoSql::gerar($usuarioId, VerificacaoSql::TELEFONE, 'whatsapp', $usuario['telefone']);
                    Whatsapp::enviarCodigo($usuario['telefone'], $codigo, VerificacaoSql::VALIDADE_MIN);
                } catch (RuntimeException $e) {
                    error_log('Login: falha ao enviar código de telefone.');
                }
            }
            helpers::resposta_json(
                false,
                'Falta confirmar seu telefone. Enviamos um código pelo WhatsApp.',
                ['redirect' => 'verificar-telefone.php', 'nao_verificado' => true],
                403
            );
        }
    }

    // Regenera o ID de sessão após autenticação (previne fixation).
    session_regenerate_id(true);
    $_SESSION['usuario_id']    = $usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome'];
    $_SESSION['usuario_admin'] = $usuario['admin'];
    unset($_SESSION['pendente_verificacao']);
    $redirect = $usuario['admin'] ? 'admin/dashboard.php' : 'user/agendamentos.php';
    helpers::resposta_json(true, 'Login realizado com sucesso.', ['redirect' => $redirect], 200);
}

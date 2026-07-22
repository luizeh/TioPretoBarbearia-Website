<?php
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../sql/VerificacaoSql.php';
require_once __DIR__ . '/../helpers/CadastroPendente.php';
helpers::iniciarSessao();

// Descobre a origem: cadastro pendente (na sessão) ou conta já existente (login).
$pendente = $_SESSION['pendente_verificacao'] ?? null;
if (CadastroPendente::existe()) {
    $dados = CadastroPendente::dados();
    $email = (string) $dados['email'];
} elseif (!empty($pendente['usuario_id'])) {
    $email = (string) ($pendente['email'] ?? '');
} else {
    // Sem verificação pendente não há o que confirmar aqui.
    header('Location: login.php');
    exit;
}

$csrf = helpers::tokenCsrf();

// Mascara o e-mail exibido: primeira letra + *** + domínio.
$emailMascarado = $email;
if (strpos($email, '@') !== false) {
    [$usuario, $dominio] = explode('@', $email, 2);
    $inicio = mb_substr($usuario, 0, 1);
    $emailMascarado = $inicio . str_repeat('*', max(3, mb_strlen($usuario) - 1)) . '@' . $dominio;
}

$rootPath  = '../';
$pageTitle = 'Verificar e-mail — Tio Preto Barbearia';
$extraCss  = ['assets/css/auth/login.css', 'assets/css/auth/cadastro.css'];
include_once __DIR__ . '/partials/head_public.php';
?>
<!-- ══════════════ VERIFICAÇÃO DE E-MAIL ══════════════ -->
<main class="login-section">
    <div class="login-card">

        <div class="card-header">
            <h1 class="card-brand-name">Tio Preto</h1>
            <span class="card-brand-sub">Barbearia</span>
            <p class="card-access-text">Confirme seu e-mail</p>
        </div>

        <p class="verify-intro">
            Enviamos um código de 6 dígitos para<br>
            <strong><?= htmlspecialchars($emailMascarado) ?></strong>.
            O código expira em <?= (int) VerificacaoSql::VALIDADE_MIN ?> minutos.
        </p>

        <p class="verify-spam-hint">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Não encontrou o e-mail? Verifique a caixa de <strong>Spam</strong> ou <strong>Promoções</strong>.
        </p>

        <form id="form-verificar" autocomplete="one-time-code">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>" />

            <div class="form-group">
                <label class="form-label" for="codigo">Código de verificação</label>
                <div class="input-wrap">
                    <input class="form-input verify-code-input" type="text" id="codigo" name="codigo"
                        inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="000000"
                        autocomplete="one-time-code" required />
                </div>
                <small class="field-error" data-error-for="codigo" hidden></small>
            </div>

            <button type="submit" class="btn-login" id="btn-verificar">
                <i class="fa-solid fa-circle-check"></i>&nbsp; Verificar
            </button>
        </form>

        <p class="form-footer-link verify-resend">
            Não recebeu?&nbsp;
            <button type="button" id="btn-reenviar" class="link-button">Reenviar código</button>
            <span id="reenvio-status" class="verify-resend__status" aria-live="polite"></span>
        </p>

        <p class="form-footer-link">
            <a href="login.php">Voltar ao login</a>
        </p>

    </div>
</main>

<footer class="login-footer-simple">
    <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados. &nbsp;|&nbsp; Feito por <span>Luizeh</span></p>
</footer>

<script src="../assets/js/auth/verificar-email.js" defer></script>
</body>

</html>

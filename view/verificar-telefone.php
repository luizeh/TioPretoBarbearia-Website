<?php
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../sql/VerificacaoSql.php';
require_once __DIR__ . '/../helpers/CadastroPendente.php';
helpers::iniciarSessao();

// Descobre a origem: cadastro pendente (na sessão) ou conta já existente (login).
$pendente = $_SESSION['pendente_verificacao'] ?? null;
if (CadastroPendente::existe()) {
    $dados       = CadastroPendente::dados();
    $telefoneRaw = $dados['telefone'];
} elseif (!empty($pendente['usuario_id'])) {
    $telefoneRaw = $pendente['telefone'] ?? '';
} else {
    // Sem verificação pendente não há o que confirmar aqui.
    header('Location: login.php');
    exit;
}

$csrf     = helpers::tokenCsrf();
$telefone = preg_replace('/\D/', '', (string) $telefoneRaw);

// Máscara: mostra só os 4 últimos dígitos.
$telefoneMascarado = 'seu WhatsApp';
if (strlen($telefone) >= 4) {
    $ultimos = substr($telefone, -4);
    $telefoneMascarado = '(••) •••••-' . $ultimos;
}

$rootPath  = '../';
$pageTitle = 'Verificar telefone — Tio Preto Barbearia';
$extraCss  = ['assets/css/auth/login.css', 'assets/css/auth/cadastro.css'];
include_once __DIR__ . '/partials/head_public.php';
?>
<!-- ══════════════ VERIFICAÇÃO DE TELEFONE ══════════════ -->
<main class="login-section">
    <div class="login-card">

        <div class="card-header">
            <h1 class="card-brand-name">Tio Preto</h1>
            <span class="card-brand-sub">Barbearia</span>
            <p class="card-access-text">Confirme seu telefone</p>
        </div>

        <p class="verify-intro">
            <i class="fa-brands fa-whatsapp" style="color:#25d366;"></i>
            Enviamos um código de 6 dígitos pelo WhatsApp para<br>
            <strong><?= htmlspecialchars($telefoneMascarado) ?></strong>.
            O código expira em <?= (int) VerificacaoSql::VALIDADE_MIN ?> minutos.
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

<script src="../assets/js/auth/verificar-telefone.js" defer></script>
</body>

</html>

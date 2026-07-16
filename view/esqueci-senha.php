<?php
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../sql/VerificacaoSql.php';
helpers::iniciarSessao();

$csrf      = helpers::tokenCsrf();
$rootPath  = '../';
$pageTitle = 'Recuperar senha — Tio Preto Barbearia';
$extraCss  = ['assets/css/auth/login.css', 'assets/css/auth/cadastro.css'];
include_once __DIR__ . '/partials/head_public.php';
?>
<!-- ══════════════ RECUPERAÇÃO DE SENHA ══════════════ -->
<main class="login-section">
    <div class="login-card">

        <div class="card-header">
            <h1 class="card-brand-name">Tio Preto</h1>
            <span class="card-brand-sub">Barbearia</span>
            <p class="card-access-text">Recuperar acesso</p>
        </div>

        <input type="hidden" id="csrf_token" value="<?= htmlspecialchars($csrf) ?>" />

        <!-- Passo 1: escolher canal e informar o contato -->
        <form id="form-solicitar" class="recover-step" data-step="solicitar">
            <p class="verify-intro">Como deseja receber o código de recuperação?</p>

            <div class="recover-channel">
                <label class="recover-channel__opt">
                    <input type="radio" name="canal" value="email" checked />
                    <span><i class="fa-solid fa-envelope"></i> E-mail</span>
                </label>
                <label class="recover-channel__opt">
                    <input type="radio" name="canal" value="whatsapp" />
                    <span><i class="fa-brands fa-whatsapp"></i> WhatsApp</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="identificador" id="ident-label">E-mail cadastrado</label>
                <div class="input-wrap">
                    <input class="form-input" type="text" id="identificador" name="identificador"
                        placeholder="seu@email.com" required autocomplete="off" />
                </div>
                <small class="field-error" data-error-for="identificador" hidden></small>
            </div>

            <button type="submit" class="btn-login" id="btn-solicitar">
                <i class="fa-solid fa-paper-plane"></i>&nbsp; Enviar código
            </button>
        </form>

        <!-- Passo 2: validar o código -->
        <form id="form-codigo" class="recover-step" data-step="codigo" hidden>
            <p class="verify-intro" id="codigo-intro">
                Enviamos um código de 6 dígitos. Ele expira em <?= (int) VerificacaoSql::VALIDADE_MIN ?> minutos.
            </p>

            <div class="form-group">
                <label class="form-label" for="codigo">Código de verificação</label>
                <div class="input-wrap">
                    <input class="form-input verify-code-input" type="text" id="codigo" name="codigo"
                        inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="000000"
                        autocomplete="one-time-code" required />
                </div>
                <small class="field-error" data-error-for="codigo" hidden></small>
            </div>

            <button type="submit" class="btn-login" id="btn-validar">
                <i class="fa-solid fa-circle-check"></i>&nbsp; Validar código
            </button>

            <p class="form-footer-link verify-resend">
                Não recebeu?&nbsp;
                <button type="button" id="btn-reenviar" class="link-button">Reenviar código</button>
                <span id="reenvio-status" class="verify-resend__status" aria-live="polite"></span>
            </p>
        </form>

        <!-- Passo 3: definir nova senha -->
        <form id="form-nova-senha" class="recover-step" data-step="senha" hidden>
            <p class="verify-intro">Defina sua nova senha.</p>

            <div class="form-group">
                <label class="form-label" for="nova_senha">Nova senha</label>
                <div class="input-wrap">
                    <input class="form-input" type="password" id="nova_senha" name="nova_senha"
                        placeholder="Mínimo 8 caracteres" required minlength="8" autocomplete="new-password" />
                </div>
                <small class="field-error" data-error-for="nova_senha" hidden></small>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirmar_senha">Confirmar nova senha</label>
                <div class="input-wrap">
                    <input class="form-input" type="password" id="confirmar_senha" name="confirmar_senha"
                        placeholder="Repita a senha" required minlength="8" autocomplete="new-password" />
                </div>
                <small class="field-error" data-error-for="confirmar_senha" hidden></small>
            </div>

            <button type="submit" class="btn-login" id="btn-redefinir">
                <i class="fa-solid fa-key"></i>&nbsp; Redefinir senha
            </button>
        </form>

        <p class="form-footer-link">
            <a href="login.php">Voltar ao login</a>
        </p>

    </div>
</main>

<footer class="login-footer-simple">
    <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados. &nbsp;|&nbsp; Feito por <span>Luizeh</span></p>
</footer>

<script src="../assets/js/auth/esqueci-senha.js" defer></script>
</body>

</html>

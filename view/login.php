<?php
require_once __DIR__ . '/../helpers/helpers.php';
$csrf      = helpers::tokenCsrf();
$rootPath  = '../';
$pageTitle = 'Login — Tio Preto Barbearia';
$extraCss  = ['assets/css/auth/login.css'];
include_once __DIR__ . '/partials/head_public.php';
?>
<!-- ══════════════ LOGIN ══════════════ -->
<main class="login-section">
  <div class="login-card">

    <!-- Brand dentro do card -->
    <div class="card-header">
      <h1 class="card-brand-name">Tio Preto</h1>
      <span class="card-brand-sub">Barbearia</span>
      <p class="card-access-text">Acesso ao Painel</p>
    </div>

    <form id="form-login" action="../api/auth/login.php" method="POST">
      <input type="hidden" name="action" value="login">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

      <div class="form-group">
        <label class="form-label" for="email">E-mail</label>
        <div class="input-wrap">
          <input
            class="form-input"
            type="email"
            id="email"
            name="email"
            placeholder="seu@email.com"
            required
            autocomplete="email" />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="senha">Senha</label>
        <div class="input-wrap">
          <input
            class="form-input"
            type="password"
            id="senha"
            name="senha"
            placeholder="••••••••"
            required
            autocomplete="current-password" />
        </div>
      </div>

      <button type="submit" class="btn-login">Entrar</button>
    </form>

    <p class="form-footer-link">
      <a href="esqueci-senha.php">Esqueci minha senha</a>
    </p>

    <p class="form-footer-link">
      Ainda não tem uma conta? &nbsp;<a href="cadastro.php">Criar conta</a>
    </p>

  </div>
</main>

<script src="../assets/js/auth/login.js" defer></script>
</body>

</html>

<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — Tio Preto Barbearia</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/login.css" />
  </head>
  <body>
    <!-- ══════════════ HEADER ══════════════ -->
    <header>
      <div class="header-logo">
        <img src="../img/tiopretonb.png" alt="Tio Preto Barbearia" />
        <div class="brand-text">
          <span class="brand-name">Tio Preto</span>
          <span class="brand-sub">Barbearia</span>
        </div>
      </div>
      <nav>
        <a href="../view/login.php#">Início</a>
        <a href="mailto:contato@tiopretobarbearia.com">Contato</a>
      </nav>
    </header>

    <!-- ══════════════ LOGIN ══════════════ -->
    <main class="login-section">
      <div class="login-card">
        <span class="login-eyebrow">✦ Área Restrita</span>
        <h1 class="login-title">Bem-vindo de<br /><span>volta</span>.</h1>
        <p class="login-subtitle">Acesse o painel da barbearia</p>
        <div class="login-divider"></div>

        <form action="../api/login.php" method="POST">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <label class="form-label" for="usuario">Usuário</label>
            <div class="input-wrap">
              <i class="fa-regular fa-user"></i>
              <input
                class="form-input"
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Digite seu usuário"
                required
                autocomplete="username"
              />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="senha">Senha</label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock"></i>
              <input
                class="form-input"
                type="password"
                id="senha"
                name="senha"
                placeholder="Digite sua senha"
                required
                autocomplete="current-password"
              />
            </div>
          </div>

          <div class="form-options">
            <label class="form-check">
              <input type="checkbox" name="lembrar" />
              <span>Lembrar-me</span>
            </label>
            <a href="#" class="forgot-link">Esqueceu a senha?</a>
          </div>

          <button type="submit" class="btn-login">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp; Entrar
          </button>
        </form>
      </div>
    </main>

    <!-- ══════════════ FOOTER SIMPLES ══════════════ -->
    <footer class="login-footer-simple">
      <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados. &nbsp;|&nbsp; Feito por <span>Luizeh</span></p>
    </footer>
  </body>
</html>
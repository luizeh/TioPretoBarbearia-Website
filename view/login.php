<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="../img/favicon.png" type="image/png" />
  <title>Login — Tio Preto Barbearia</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
  <!-- ══════════════ LOGIN ══════════════ -->
  <main class="login-section">
    <div class="login-card">

      <!-- Brand dentro do card -->
      <div class="card-header">
        <h1 class="card-brand-name">Tio Preto</h1>
        <span class="card-brand-sub">Barbearia</span>
        <p class="card-access-text">Acesso ao Painel</p>
      </div>

      <form action="../api/login.php" method="POST">
        <input type="hidden" name="action" value="login">

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
        Ainda não tem uma conta? &nbsp;<a href="cadastro.php">Criar conta</a>
      </p>

    </div>
  </main>

  <!-- ══════════════ FOOTER SIMPLES ══════════════ -->
  <footer class="login-footer-simple">
    <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados. &nbsp;|&nbsp; Feito por <span>Luizeh</span></p>
  </footer>
</body>

</html>
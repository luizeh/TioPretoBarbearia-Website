<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/png" />
    <title>Cadastro — Tio Preto Barbearia</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/login.css" />
    <link rel="stylesheet" href="../assets/css/cadastro.css" />

</head>

<body>
    <!-- ══════════════ CADASTRO ══════════════ -->
    <main class="login-section">
        <div class="login-card">

            <!-- Brand dentro do card -->
            <div class="card-header">
                <h1 class="card-brand-name">Tio Preto</h1>
                <span class="card-brand-sub">Barbearia</span>
                <p class="card-access-text">Criar sua conta</p>
            </div>

            <form action="../api/auth/cadastro.php" method="POST" autocomplete="off">
                <input type="hidden" name="action" value="cadastro" />

                <!-- Nome e Sobrenome -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome">Nome</label>
                        <div class="input-wrap">
                            <input class="form-input" type="text" id="nome" name="nome"
                                placeholder="Seu nome" required autocomplete="given-name" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sobrenome">Sobrenome</label>
                        <div class="input-wrap">
                            <input class="form-input" type="text" id="sobrenome" name="sobrenome"
                                placeholder="Seu sobrenome" required autocomplete="family-name" />
                        </div>
                    </div>
                </div>

                <!-- Telefone e Cidade -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="telefone">Telefone</label>
                        <div class="input-wrap">
                            <input class="form-input" type="tel" id="telefone" name="telefone"
                                placeholder="(00) 9 0000-0000" required autocomplete="tel" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cidade">Cidade</label>
                        <div class="input-wrap">
                            <input class="form-input" type="text" id="cidade" name="cidade"
                                placeholder="Sua cidade" required autocomplete="address-level2" />
                        </div>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="form-group">
                    <label class="form-label" for="email">E-mail</label>
                    <div class="input-wrap">
                        <input class="form-input" type="email" id="email" name="email"
                            placeholder="seu@email.com" required autocomplete="email" />
                    </div>
                </div>

                <!-- Senha e Confirmar Senha -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="senha">Senha</label>
                        <div class="input-wrap">
                            <input class="form-input" type="password" id="senha" name="senha"
                                placeholder="Mínimo 6 caracteres" required minlength="6"
                                autocomplete="new-password" oninput="avaliarSenha(this.value)" />
                        </div>
                        <div class="senha-strength" id="strength-bar">
                            <span id="s1"></span>
                            <span id="s2"></span>
                            <span id="s3"></span>
                            <span id="s4"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirmar_senha">Confirmar senha</label>
                        <div class="input-wrap">
                            <input class="form-input" type="password" id="confirmar_senha"
                                name="confirmar_senha" placeholder="Repita a senha" required
                                autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-user-plus"></i>&nbsp; Criar conta
                </button>
            </form>

            <p class="form-footer-link">
                Já tem conta? &nbsp;<a href="login.php">Entrar</a>
            </p>

        </div>
    </main>

    <!-- ══════════════ FOOTER SIMPLES ══════════════ -->
    <footer class="login-footer-simple">
        <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados. &nbsp;|&nbsp; Feito por <span>Luizeh</span></p>
    </footer>

    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/cadastro.js"></script>
</body>

</html>
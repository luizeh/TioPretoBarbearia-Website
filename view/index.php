<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$logado = !empty($_SESSION['usuario_id']);

$rootPath  = '../';
$pageTitle = 'Tio Preto Barbearia';
$bodyClass = 'landing-page';

require_once __DIR__ . '/../sql/SiteConfigSql.php';
$_landing = SiteConfigSql::buscarGrupo('landing');
// Função inline: retorna valor do DB com fallback hardcoded
$lnd = static function (string $k, string $f) use ($_landing): string {
  return htmlspecialchars($_landing[$k] ?? $f);
};

include_once __DIR__ . '/partials/head_public.php';
?>

<section class="hero hero--landing">
  <div class="hero-content">

    <!-- Logo -->
    <div class="hero-brand">
      <img src="../assets/img/tiopretonb.png" alt="Tio Preto Barbearia" />
    </div>

    <p class="hero-eyebrow"><?= $lnd('hero_eyebrow', '✦ Estilo &amp; Tradição') ?></p>
    <h1 class="hero-title"><?= $lnd('hero_titulo', 'Arte em cada corte.') ?></h1>
    <p class="hero-desc">
      <?= $lnd('hero_descricao', 'Na Tio Preto Barbearia, cada detalhe é pensado para realçar o melhor de você — do corte à barba, com produtos premium e técnica impecável.') ?>
    </p>

    <div class="hero-cta">
      <?php if (!$logado): ?>
        <a href="login.php" class="btn-hero-primary">
          <i class="fa-solid fa-right-to-bracket"></i> Entrar
        </a>
        <a href="cadastro.php" class="btn-hero-primary">
          <i class="fa-solid fa-user-plus"></i> Criar Conta
        </a>
        <div class="hero-cta__break"></div>
      <?php endif; ?>
      <a href="user/agendamentos.php" class="btn-hero-secondary">
        <i class="fa-solid fa-calendar-plus"></i> Agende seu Horário &rarr;
      </a>
      <a href="catalogo.php" class="btn-hero-secondary">
        <i class="fa-solid fa-store"></i> Ver Produtos &rarr;
      </a>
    </div>

  </div>

  <div class="hero-image-side">
    <img src="../assets/img/barbearia.png" alt="Tio Preto Barbearia" />
    <div class="hero-overlay"></div>
  </div>
</section>

<?php include_once('partials/footer.php') ?>
</body>

</html>
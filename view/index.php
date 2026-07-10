<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Redireciona usuários já logados direto para o site
if (!empty($_SESSION['usuario_id'])) {
  if (!empty($_SESSION['usuario_admin'])) {
    header('Location: admin/dashboard.php');
  } else {
    header('Location: catalogo.php');
  }
  exit;
}

$rootPath  = '../';
$pageTitle = 'Tio Preto Barbearia';
$bodyClass = 'landing-page';
include_once __DIR__ . '/partials/head_public.php';
?>

<section class="hero hero--landing">
  <div class="hero-content">

    <!-- Logo -->
    <div class="hero-brand">
      <img src="../assets/img/tiopretonb.png" alt="Tio Preto Barbearia" />
    </div>

    <p class="hero-eyebrow">✦ Estilo &amp; Tradição</p>
    <h1 class="hero-title">Arte em cada<br /><span>corte</span>.</h1>
    <p class="hero-desc">
      Na Tio Preto Barbearia, cada detalhe é pensado para realçar o melhor
      de você — do corte à barba, com produtos premium e técnica impecável.
    </p>

    <!-- CTAs (só para visitantes não logados) -->
    <div class="hero-cta">
      <a href="login.php" class="btn-hero-primary">
        <i class="fa-solid fa-right-to-bracket"></i> Entrar
      </a>
      <a href="cadastro.php" class="btn-hero-primary">
        <i class="fa-solid fa-user-plus"></i> Criar Conta
      </a>
      <div class="hero-cta__break"></div>
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

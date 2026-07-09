<?php
$rootPath  = '../../';
$linkBase  = '../';
$activeNav = 'servicos';
$pageTitle = 'Serviços — Tio Preto Barbearia';
$bodyClass = 'user-page';
include_once __DIR__ . '/../partials/head_public.php';
include_once __DIR__ . '/../../config/connection.php';
include_once __DIR__ . '/../../sql/ServicosSql.php';
$servicos = ServicosSql::listar();
?>
<!-- ══════════════ HEADER ══════════════ -->
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>

<!-- ══════════════ BANNER ══════════════ -->
<div class="page-banner">
    <span class="page-banner__eyebrow">✦ Tio Preto Barbearia</span>
    <h1 class="page-banner__title">Nossos <span>Serviços</span></h1>
    <p class="page-banner__desc">Escolha o serviço ideal e agende o seu horário com facilidade.</p>
</div>

<!-- ══════════════ SERVIÇOS ══════════════ -->
<section id="servicos">
    <div class="section-header">
        <span class="section-eyebrow">O que oferecemos</span>
        <h2 class="section-title">Para o seu Estilo</h2>
    </div>

    <div class="services-grid">

        <?php foreach ($servicos as $s): ?>
            <div class="service-card fade-in">
                <div class="service-icon-wrap">
                    <div class="service-icon-inner">
                        <i class="fa-solid fa-scissors"></i>
                    </div>
                    <span class="service-badge"><?= (int)$s['tempo_estimado'] ?> min</span>
                </div>
                <div class="service-body">
                    <h3 class="service-name"><?= htmlspecialchars($s['nome']) ?></h3>
                    <p class="service-desc"><?= htmlspecialchars($s['descricao'] ?? '') ?></p>
                    <div class="service-meta">
                        <span class="service-duration">
                            <i class="fa-regular fa-clock"></i> <?= (int)$s['tempo_estimado'] ?> min
                        </span>
                        <span class="service-price">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                    </div>
                    <div class="service-cta">
                        <a href="agendamentos.php" class="btn-service-book">
                            <i class="fa-solid fa-calendar-plus"></i> Agendar
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($servicos)): ?>
            <p style="text-align:center;opacity:.5;padding:40px 0;">Nenhum serviço disponível no momento.</p>
        <?php endif; ?>

    </div>
</section>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
</body>

</html>
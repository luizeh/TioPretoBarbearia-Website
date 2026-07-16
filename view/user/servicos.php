<?php
$rootPath  = '../../';
$linkBase  = '../';
$activeNav = 'servicos';
$pageTitle = 'Serviços — Tio Preto Barbearia';
$bodyClass = 'user-page';
include_once __DIR__ . '/../partials/head_public.php';
include_once __DIR__ . '/../../config/Connection.php';
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
<main>
<section id="servicos">
    <div class="section-header">
        <span class="section-eyebrow">O que oferecemos</span>
        <h2 class="section-title">Para o seu Estilo</h2>
    </div>

    <div class="products-grid">

        <?php foreach ($servicos as $s): ?>
            <div class="product-card fade-in">
                <div class="product-image-wrap">
                    <?php if (!empty($s['foto_url'])): ?>
                        <img src="../../<?= htmlspecialchars($s['foto_url']) ?>" alt="<?= htmlspecialchars($s['nome']) ?>" loading="lazy" data-hide-on-error />
                    <?php else: ?>
                        <div class="product-image-placeholder"><i class="fa-solid fa-scissors"></i></div>
                    <?php endif; ?>
                    <span class="product-badge"><?= (int)$s['tempo_estimado'] ?> min</span>
                </div>
                <div class="product-body">
                    <h3 class="product-name"><?= htmlspecialchars($s['nome']) ?></h3>
                    <p class="product-desc"><?= htmlspecialchars($s['descricao'] ?? '') ?></p>
                    <div class="product-tags">
                        <span class="product-tag"><i class="fa-regular fa-clock"></i> <?= (int)$s['tempo_estimado'] ?> min</span>
                    </div>
                    <div class="product-footer">
                        <span class="product-price">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                        <a href="agendamentos.php" class="btn-cart"><i class="fa-solid fa-calendar-plus"></i> Agendar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($servicos)): ?>
            <p class="catalog-empty-state">Nenhum serviço disponível no momento.</p>
        <?php endif; ?>

    </div>
</section>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
</body>

</html>

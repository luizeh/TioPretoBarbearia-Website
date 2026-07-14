<?php
$rootPath  = '../';
$linkBase  = '';
$activeNav = 'produtos';
$pageTitle = 'Produtos — Tio Preto Barbearia';
include_once __DIR__ . '/partials/head_public.php';
include_once __DIR__ . '/../config/connection.php';
include_once __DIR__ . '/../sql/ProdutosSql.php';
$pdo     = Connection::getConnection();
$produtos = ProdutosSql::listarProdutos($pdo, true); // catálogo público: só produtos visíveis
?>
<!-- ══════════════ HEADER ══════════════ -->
<?php include_once __DIR__ . '/partials/header_public.php'; ?>

<!-- ══════════════ BANNER ══════════════ -->
<div class="page-banner">
  <span class="page-banner__eyebrow">✦ Tio Preto Barbearia</span>
  <h1 class="page-banner__title">Nossos <span>Produtos</span></h1>
  <p class="page-banner__desc">Conheça a linha Pomade Million, desenvolvida para realçar o seu estilo.</p>
</div>

<!-- ══════════════ PRODUTOS ══════════════ -->
<section id="produtos">
  <div class="section-header">
    <span class="section-eyebrow">Linha de Produtos</span>
    <h2 class="section-title">Para o seu Estilo</h2>
  </div>

  <div class="products-grid">
    <?php foreach ($produtos as $p):
      $tagList  = !empty($p['tags']) ? array_map('trim', explode(',', $p['tags'])) : [];
      $badge    = $tagList[0] ?? null;
    ?>
      <div class="product-card fade-in">

        <!-- ── Imagem ── -->
        <div class="product-image-wrap">
          <?php if (!empty($p['foto_url'])): ?>
            <img
              src="../<?= htmlspecialchars($p['foto_url']) ?>"
              alt="<?= htmlspecialchars($p['nome']) ?>"
              data-hide-on-error />
          <?php else: ?>
            <div class="product-image-placeholder"></div>
          <?php endif; ?>
          <?php if ($badge): ?>
            <span class="product-badge"><?= htmlspecialchars($badge) ?></span>
          <?php endif; ?>
        </div>

        <!-- ── Corpo ── -->
        <div class="product-body">
          <h3 class="product-name"><?= htmlspecialchars($p['nome']) ?></h3>
          <p class="product-desc"><?= htmlspecialchars($p['descricao'] ?? '') ?></p>

          <?php if (!empty($tagList)): ?>
            <div class="product-tags">
              <?php foreach ($tagList as $tag): ?>
                <span class="product-tag"><?= htmlspecialchars($tag) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="product-footer">
            <span class="product-price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
            <?php if ((int) $p['estoque'] > 0): ?>
              <button class="btn-cart" data-add-cart data-product-id="<?= $p['id'] ?>">
                <i class="fa-solid fa-bag-shopping"></i> Adicionar
              </button>
            <?php else: ?>
              <span class="product-out-of-stock">Sem estoque</span>
            <?php endif; ?>
          </div>
        </div>

      </div>
    <?php endforeach; ?>

    <?php if (empty($produtos)): ?>
      <p class="produtos-vazio">Nenhum produto disponível no momento.</p>
    <?php endif; ?>
  </div>
</section>

<?php include_once('partials/footer.php') ?>
</body>

</html>

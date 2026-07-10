<?php
include_once __DIR__ . '/../../api/auth/session.php';
require_once __DIR__ . '/../../sql/CarrinhoSql.php';
$rootPath = '../../';
$linkBase = '../';
$pageTitle = 'Meu Carrinho';
$bodyClass = 'user-page';
$carrinhoId = CarrinhoSql::buscarOuCriar((int) $_SESSION['usuario_id']);
$itens = CarrinhoSql::listarItens($carrinhoId);
$total = array_sum(array_column($itens, 'subtotal'));
?>
<?php include_once __DIR__ . '/../partials/head_public.php'; ?>
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>

<div class="page-banner">
    <span class="page-banner__eyebrow">Área do Cliente</span>
    <h1 class="page-banner__title">Meu Carrinho</h1>
    <p class="page-banner__desc">Revise seus produtos antes de fazer o pedido.</p>
</div>

<main class="cart-page user-agenda">
    <section class="cart-page__items">
        <div class="cart-page__header"><h2><i class="fa-solid fa-bag-shopping"></i> Produtos selecionados</h2><a href="../catalogo.php">Continuar comprando</a></div>
        <div id="cart-page-items">
            <?php foreach ($itens as $item): ?>
                <article class="cart-page-item" data-cart-page-item="<?= (int) $item['id'] ?>">
                    <?php if (!empty($item['foto_url'])): ?><img src="../../<?= htmlspecialchars($item['foto_url']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>" /><?php else: ?><div class="cart-page-item__placeholder"><i class="fa-solid fa-box"></i></div><?php endif; ?>
                    <div class="cart-page-item__info"><h3><?= htmlspecialchars($item['nome']) ?></h3><span>R$ <?= number_format($item['preco'], 2, ',', '.') ?></span></div>
                    <div class="cart-page-item__quantity"><button type="button" data-page-dec="<?= (int) $item['id'] ?>">−</button><strong><?= (int) $item['quantidade'] ?></strong><button type="button" data-page-inc="<?= (int) $item['id'] ?>">+</button></div>
                    <strong class="cart-page-item__subtotal">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></strong>
                    <button type="button" class="cart-page-item__remove" data-page-remove="<?= (int) $item['id'] ?>" title="Remover"><i class="fa-solid fa-trash"></i></button>
                </article>
            <?php endforeach; ?>
            <?php if (!$itens): ?><div class="cart-page__empty"><i class="fa-solid fa-bag-shopping"></i><p>Seu carrinho está vazio.</p><a href="../catalogo.php" class="btn-cart">Ver produtos</a></div><?php endif; ?>
        </div>
    </section>

    <?php if ($itens): ?>
        <aside class="cart-page__summary">
            <h2>Resumo do pedido</h2>
            <div class="cart-page__total"><span>Total</span><strong id="cart-page-total">R$ <?= number_format($total, 2, ',', '.') ?></strong></div>
            <label class="modal-label" for="cart-page-address">Endereço de entrega</label>
            <textarea class="modal-textarea" id="cart-page-address" rows="4" placeholder="Rua, número, bairro e cidade"></textarea>
            <button type="button" class="btn-cart cart-page__checkout" id="cart-page-checkout"><i class="fa-solid fa-receipt"></i> Fazer pedido</button>
        </aside>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/cart-page.js" defer></script>
</body>
</html>

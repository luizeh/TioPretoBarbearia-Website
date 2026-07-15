<?php
include_once __DIR__ . '/../../api/auth/session.php';
require_once __DIR__ . '/../../sql/CarrinhoSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';
$rootPath = '../../';
$linkBase = '../';
$pageTitle = 'Meu Carrinho';
$bodyClass = 'user-page';
$csrf = helpers::tokenCsrf();
$carrinhoId = CarrinhoSql::buscarOuCriar((int) $_SESSION['usuario_id']);
$itens = CarrinhoSql::listarItens($carrinhoId);
$total = array_sum(array_column($itens, 'subtotal'));
$ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
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

            <form class="modal-form cart-address-form" id="cart-address-form" autocomplete="on" novalidate>
                <input type="hidden" id="cart-csrf" value="<?= htmlspecialchars($csrf) ?>">
                <h3 class="cart-address-form__title"><i class="fa-solid fa-location-dot"></i> Endereço de entrega</h3>

                <div class="modal-field">
                    <label class="modal-label" for="cart-cep">CEP</label>
                    <div class="cart-cep-wrap">
                        <input class="modal-input" type="text" id="cart-cep" name="cep" inputmode="numeric" maxlength="9" placeholder="00000-000" autocomplete="postal-code" required>
                        <span class="cart-cep-status" id="cart-cep-status" aria-live="polite"></span>
                    </div>
                    <small class="field-error" data-error-for="cep" hidden></small>
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="cart-logradouro">Logradouro</label>
                    <input class="modal-input" type="text" id="cart-logradouro" name="logradouro" maxlength="150" placeholder="Rua, avenida…" autocomplete="address-line1" required>
                    <small class="field-error" data-error-for="logradouro" hidden></small>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label" for="cart-numero">Número</label>
                        <input class="modal-input" type="text" id="cart-numero" name="numero" maxlength="20" placeholder="Nº ou S/N" required>
                        <small class="field-error" data-error-for="numero" hidden></small>
                    </div>
                    <div class="modal-field">
                        <label class="modal-label" for="cart-bairro">Bairro</label>
                        <input class="modal-input" type="text" id="cart-bairro" name="bairro" maxlength="100" placeholder="Bairro" autocomplete="address-level3" required>
                        <small class="field-error" data-error-for="bairro" hidden></small>
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label" for="cart-cidade">Cidade</label>
                        <input class="modal-input" type="text" id="cart-cidade" name="cidade" maxlength="100" placeholder="Cidade" autocomplete="address-level2" required>
                        <small class="field-error" data-error-for="cidade" hidden></small>
                    </div>
                    <div class="modal-field cart-field--uf">
                        <label class="modal-label" for="cart-estado">Estado</label>
                        <select class="modal-select" id="cart-estado" name="estado" autocomplete="address-level1" required>
                            <option value="">UF</option>
                            <?php foreach ($ufs as $uf): ?>
                                <option value="<?= $uf ?>"><?= $uf ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="field-error" data-error-for="estado" hidden></small>
                    </div>
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="cart-complemento">Complemento <span class="cart-optional">(opcional)</span></label>
                    <input class="modal-input" type="text" id="cart-complemento" name="complemento" maxlength="150" placeholder="Apto, bloco, casa…" autocomplete="address-line2">
                    <small class="field-error" data-error-for="complemento" hidden></small>
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="cart-referencia">Ponto de referência <span class="cart-optional">(opcional)</span></label>
                    <input class="modal-input" type="text" id="cart-referencia" name="ponto_referencia" maxlength="150" placeholder="Próximo a…">
                    <small class="field-error" data-error-for="ponto_referencia" hidden></small>
                </div>

                <button type="submit" class="btn-cart cart-page__checkout" id="cart-page-checkout"><i class="fa-solid fa-receipt"></i> Fazer pedido</button>
            </form>
        </aside>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/cart-page.js" defer></script>
</body>
</html>

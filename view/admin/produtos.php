<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';

$activePage = 'produtos';
$pageTitle  = 'Produtos';
include_once(__DIR__ . '/../../controllers/produtos.controller.php');
include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-wrapper">

  <?php include __DIR__ . '/../partials/topbar.php'; ?>

  <main class="page-content">

    <div class="page-header">
      <div>
        <p class="page-eyebrow">✦ Estoque</p>
        <h1 class="page-title page-title--lg">Produtos</h1>
      </div>
      <button class="btn-primary" data-modal="modal-produto">
        <i class="fa-solid fa-plus"></i> Novo Produto
      </button>
    </div>

    <div class="view-toggle">
      <a class="view-toggle__btn active" href="produtos.php"><i class="fa-solid fa-box-open"></i> Produtos</a>
      <a class="view-toggle__btn" href="pedidos.php"><i class="fa-solid fa-receipt"></i> Pedidos</a>
    </div>

    <div class="clientes-stat-banner clientes-stat-banner--full">
      <div class="clientes-stat-banner__icon">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <div class="clientes-stat-banner__info">
        <span class="clientes-stat-banner__count"><?= count($produtos) ?></span>
        <span class="clientes-stat-banner__label">produtos cadastrados</span>
      </div>
    </div>

    <div class="dashboard-card">
      <div class="dashboard-card-header">
        <h2 class="dashboard-card-title">
          <i class="fa-solid fa-box-open"></i> Todos os Produtos
        </h2>
        <div class="table-search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input class="table-search" type="text" placeholder="Pesquisar produto..." data-search="tbl-produtos" />
        </div>
      </div>
      <p class="table-scroll-hint"><i class="fa-solid fa-arrows-left-right"></i> Arraste a tabela para o lado para ver todas as colunas</p>
      <div class="table-wrapper">
        <table class="dash-table" id="tbl-produtos">
          <thead>
            <tr>
              <th>Id</th>
              <th>Nome</th>
              <th>Descrição</th>
              <th>Preço</th>
              <th>Estoque</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produtos as $produto): ?>

              <tr class="<?= empty($produto['visivel']) ? 'produto-oculto' : '' ?>">
                <td>
                  <?= number_format($produto['id']) ?>
                </td>

                <td>
                  <span class="client-name">
                    <?= htmlspecialchars($produto['nome']) ?>
                  </span>
                </td>

                <td>
                  <?= htmlspecialchars(
                    mb_strlen($produto['descricao']) > 80
                      ? mb_substr($produto['descricao'], 0, 80) . '…'
                      : $produto['descricao']
                  ) ?>
                </td>

                <td>
                  <span class="preco-badge">
                    R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                  </span>
                </td>

                <td>
                  <?= htmlspecialchars($produto['estoque']) ?> un
                </td>

                <td>
                  <div class="action-btns">
                    <button
                      class="btn-action btn-action--visibilidade"
                      data-id="<?= $produto['id'] ?>"
                      data-visivel="<?= (int) $produto['visivel'] ?>"
                      title="<?= !empty($produto['visivel']) ? 'Visível no site — clique para ocultar (só admin)' : 'Oculto (só admin) — clique para exibir no site' ?>">
                      <i class="fa-solid <?= !empty($produto['visivel']) ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                    </button>

                    <button
                      class="btn-action btn-action--edit"
                      title="Editar"
                      data-modal="modal-produto"
                      data-id="<?= $produto['id'] ?>"
                      data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                      data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                      data-estoque="<?= $produto['estoque'] ?>"
                      data-preco="<?= $produto['preco'] ?>"
                      data-tags="<?= htmlspecialchars($produto['tags'] ?? '') ?>"
                      data-tag-ids="<?= htmlspecialchars($produto['tag_ids'] ?? '') ?>"
                      data-foto-url="<?= htmlspecialchars($produto['foto_url'] ?? '') ?>">
                      <i class="fa-solid fa-pen"></i>
                    </button>

                    <button
                      class="btn-action btn-action--delete"
                      title="Excluir"
                      data-modal="modal-produto-excluir"
                      data-id="<?= $produto['id'] ?>"
                      data-nome="<?= htmlspecialchars($produto['nome']) ?>">
                      <i class="fa-solid fa-trash"></i>
                    </button>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div><!-- /.table-wrapper -->
    </div><!-- /.dashboard-card -->

    <!-- ── Tabela de Tags ── -->
    <div class="dashboard-card dashboard-card--spaced">
      <div class="dashboard-card-header">
        <h2 class="dashboard-card-title">
          <i class="fa-solid fa-tags"></i> Tags Disponíveis
        </h2>
        <button class="btn-primary btn-primary--compact" data-modal="modal-tag">
          <i class="fa-solid fa-plus"></i> Nova Tag
        </button>
      </div>
      <p class="table-scroll-hint"><i class="fa-solid fa-arrows-left-right"></i> Arraste a tabela para o lado para ver todas as colunas</p>
      <div class="table-wrapper">
        <table class="dash-table" id="tbl-tags">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tags as $tag): ?>
              <tr>
                <td><?= $tag['id'] ?></td>
                <td>
                  <span class="client-name">
                    <?= htmlspecialchars($tag['nome']) ?>
                  </span>
                </td>
                <td>
                  <div class="action-btns">
                    <button
                      class="btn-action btn-action--edit"
                      data-modal="modal-tag"
                      data-nome="<?= htmlspecialchars($tag['nome']) ?>"
                      data-id="<?= $tag['id'] ?>"
                      title="Editar">
                      <i class="fa-solid fa-pen"></i>
                    </button>

                    <button
                      class="btn-action btn-action--delete"
                      data-modal="modal-tag-excluir"
                      data-nome="<?= htmlspecialchars($tag['nome']) ?>"
                      data-id="<?= $tag['id'] ?>"
                      title="Excluir">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>


<!-- ── Modais ── -->
<?php
include __DIR__ . '/../partials/modais/modal-produto.php';
include __DIR__ . '/../partials/modais/modal-produto-ver.php';

$modal_id           = 'modal-produto-excluir';
$modal_title        = 'Excluir Produto';
$modal_entity_label = 'este produto';
include __DIR__ . '/../partials/modais/modal-excluir.php';
unset($modal_id, $modal_title, $modal_entity_label);

$modal_id           = 'modal-tag';
$modal_confirm_text = 'Salvar';
include __DIR__ . '/../partials/modais/modal-tag.php';
unset($modal_id, $modal_confirm_text);

$modal_id           = 'modal-tag-excluir';
$modal_title        = 'Excluir Tag';
$modal_entity_label = 'esta tag';
include __DIR__ . '/../partials/modais/modal-excluir.php';
unset($modal_id, $modal_title, $modal_entity_label);
?>

<?php $pageScripts = ['produtos.js', 'tags.js'];
include __DIR__ . '/../partials/scripts.php'; ?>

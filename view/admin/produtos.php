<?php
$activePage = 'produtos';
$pageTitle  = 'Produtos';
include_once(__DIR__ . '/../../api/auth/session.php');
include_once(__DIR__ . '/../../controllers/produtos.controller.php');
$usuario = ['nome' => $_SESSION['nome'] ?? 'Administrador'];
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

    <div class="clientes-stat-banner" style="max-width:100%;">
      <div class="clientes-stat-banner__icon">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <div class="clientes-stat-banner__info">
        <span class="clientes-stat-banner__count">6</span>
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

              <tr>
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
                      class="btn-action btn-action--view"
                      title="Ver"
                      data-modal="modal-produto-ver"
                      data-id="<?= $produto['id'] ?>"
                      data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                      data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                      data-estoque="<?= $produto['estoque'] ?>"
                      data-preco="<?= number_format($produto['preco'], 2, ',', '.') ?>"
                      data-tags="<?= htmlspecialchars($produto['tags'] ?? '') ?>"
                      data-foto-url="<?= htmlspecialchars($produto['foto_url'] ?? '') ?>">
                      <i class="fa-solid fa-eye"></i>
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
    <div class="dashboard-card" style="margin-top:24px;">
      <div class="dashboard-card-header">
        <h2 class="dashboard-card-title">
          <i class="fa-solid fa-tags"></i> Tags Disponíveis
        </h2>
        <button class="btn-primary" style="font-size:0.82rem;padding:8px 16px;" data-modal="modal-tag">
          <i class="fa-solid fa-plus"></i> Nova Tag
        </button>
      </div>
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
            <tr>
              <td>1</td>
              <td><span class="client-name">Cabelo</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td><span class="client-name">Barba</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td><span class="client-name">Promoção</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>4</td>
              <td><span class="client-name">Premium</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>5</td>
              <td><span class="client-name">Finalização</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div><!-- /.dashboard-card tags -->

  </main>
</div><!-- /.main-wrapper -->

<!-- ── Modal: Novo / Editar Produto ── -->
<div class="modal-overlay" id="modal-produto">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Produto</h2>
      <button class="modal-close" data-close="modal-produto"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <form class="modal-form">
        <div class="modal-field">
          <label class="modal-label">Foto do Produto</label>
          <div class="foto-dropzone" id="foto-dropzone">
            <img class="foto-dropzone__preview" id="foto-preview" src="" alt="Preview" />
            <div class="foto-dropzone__placeholder" id="foto-placeholder">
              <i class="fa-solid fa-cloud-arrow-up"></i>
              <span>Arraste ou clique para enviar</span>
              <small>JPG, PNG, WebP · máx. 2MB</small>
            </div>
            <button type="button" class="foto-dropzone__remove" id="foto-remove" title="Remover foto">✕ Remover</button>
            <input type="file" id="foto-file-input" accept="image/jpeg,image/png,image/webp,image/gif" />
            <input type="hidden" data-field="fotoUrl" id="foto-url-hidden" />
          </div>
        </div>
        <div class="modal-field">
          <label class="modal-label">Nome do Produto</label>
          <input class="modal-input" type="text" data-field="nome" placeholder="Ex: Pomada Matte" />
        </div>
        <div class="modal-field">
          <label class="modal-label">Tags</label>
          <div class="tag-picker" id="tag-picker">
            <button type="button" class="tag-option" data-tag-id="1" data-tag-nome="Cabelo">Cabelo</button>
            <button type="button" class="tag-option" data-tag-id="2" data-tag-nome="Barba">Barba</button>
            <button type="button" class="tag-option" data-tag-id="3" data-tag-nome="Promoção">Promoção</button>
            <button type="button" class="tag-option" data-tag-id="4" data-tag-nome="Premium">Premium</button>
            <button type="button" class="tag-option" data-tag-id="5" data-tag-nome="Finalização">Finalização</button>
          </div>
          <input type="hidden" data-field="tags" id="tag-hidden" name="tags" />
        </div>
        <div class="modal-row">
          <div class="modal-field">
            <label class="modal-label">Estoque (un)</label>
            <input class="modal-input" type="number" data-field="estoque" placeholder="0" min="0" />
          </div>
          <div class="modal-field">
            <label class="modal-label">Preço Unit. (R$)</label>
            <input class="modal-input" type="number" data-field="preco" placeholder="0.00" step="0.01" min="0" />
          </div>
        </div>
        <div class="modal-field">
          <label class="modal-label">Descrição</label>
          <textarea class="modal-textarea" data-field="descricao" placeholder="Descreva o produto..."></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-secondary" data-close="modal-produto">Cancelar</button>
      <button class="btn-modal-primary">Salvar</button>
    </div>
  </div>
</div>

<!-- ── Modal: Ver Produto ── -->
<div class="modal-overlay" id="modal-produto-ver">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Detalhes do Produto</h2>
      <button class="modal-close" data-close="modal-produto-ver"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <dl class="modal-info">
        <dt>Produto</dt>
        <dd data-field="nome">—</dd>
        <dt>Foto</dt>
        <dd>
          <img id="ver-foto-img" src="" alt="Foto do produto"
            style="max-width:100%;max-height:200px;border-radius:4px;display:none;" />
          <span id="ver-foto-vazio" style="color:#888;font-size:0.85rem;">Sem foto</span>
        </dd>
        <dt>Descrição</dt>
        <dd data-field="descricao">—</dd>
        <dt>Tags</dt>
        <dd data-field="tags">—</dd>
        <dt>Estoque</dt>
        <dd data-field="estoque">—</dd>
        <dt>Preço</dt>
        <dd data-field="preco">—</dd>
      </dl>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-secondary" data-close="modal-produto-ver">Fechar</button>
    </div>
  </div>
</div>

<!-- ── Modal: Excluir Produto ── -->
<div class="modal-overlay" id="modal-produto-excluir">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title"><i class="fa-solid fa-trash"></i> Excluir Produto</h2>
      <button class="modal-close" data-close="modal-produto-excluir"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-delete-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <p>Tem certeza que deseja excluir <strong data-field="nome">este produto</strong>?<br />
          Esta ação não pode ser desfeita.</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-secondary" data-close="modal-produto-excluir">Cancelar</button>
      <button class="btn-modal-danger">Excluir</button>
    </div>
  </div>
</div>

<script src="../../assets/js/produtos.js"></script>
<?php include __DIR__ . '/../partials/scripts.php'; ?>
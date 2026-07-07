<?php
$activePage = 'produtos';
$pageTitle  = 'Produtos';
include_once(__DIR__ . '/../../api/auth/session.php');
$usuario = ['nome' => $_SESSION['nome'] ?? 'Administrador'];
include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-wrapper">

  <?php include __DIR__ . '/../partials/topbar.php'; ?>

  <main class="page-content">

    <!-- Cabeçalho -->
    <div class="page-header">
      <div>
        <p class="page-eyebrow">✦ Estoque</p>
        <h1 class="page-title page-title--lg">Produtos</h1>
      </div>
      <button class="btn-primary" data-modal="modal-produto">
        <i class="fa-solid fa-plus"></i> Novo Produto
      </button>
    </div>

    <!-- Banner total -->
    <div class="clientes-stat-banner" style="max-width:100%;">
      <div class="clientes-stat-banner__icon">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <div class="clientes-stat-banner__info">
        <span class="clientes-stat-banner__count">6</span>
        <span class="clientes-stat-banner__label">produtos cadastrados</span>
      </div>
    </div>

    <!-- Tabela -->
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
              <th>Produto</th>
              <th>Categoria</th>
              <th>Estoque</th>
              <th>Preço Unit.</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="client-name">Pomada Matte</span></td>
              <td>Finalizadores</td>
              <td>12 un</td>
              <td><span class="preco-badge">R$ 45,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Pomada Matte" data-categoria="Finalizadores" data-estoque="12" data-preco="R$ 45,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Pomada Matte" data-categoria="Finalizadores" data-estoque="12" data-preco="45.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Pomada Matte"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td><span class="client-name">Shampoo Anticaspa</span></td>
              <td>Shampoos</td>
              <td>8 un</td>
              <td><span class="preco-badge">R$ 28,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Shampoo Anticaspa" data-categoria="Shampoos" data-estoque="8" data-preco="R$ 28,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Shampoo Anticaspa" data-categoria="Shampoos" data-estoque="8" data-preco="28.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Shampoo Anticaspa"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td><span class="client-name">Óleo para Barba</span></td>
              <td>Barba</td>
              <td>15 un</td>
              <td><span class="preco-badge">R$ 35,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Óleo para Barba" data-categoria="Barba" data-estoque="15" data-preco="R$ 35,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Óleo para Barba" data-categoria="Barba" data-estoque="15" data-preco="35.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Óleo para Barba"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td><span class="client-name">Cera Modeladora</span></td>
              <td>Finalizadores</td>
              <td>6 un</td>
              <td><span class="preco-badge">R$ 38,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Cera Modeladora" data-categoria="Finalizadores" data-estoque="6" data-preco="R$ 38,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Cera Modeladora" data-categoria="Finalizadores" data-estoque="6" data-preco="38.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Cera Modeladora"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td><span class="client-name">Condicionador</span></td>
              <td>Condicionadores</td>
              <td>10 un</td>
              <td><span class="preco-badge">R$ 25,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Condicionador" data-categoria="Condicionadores" data-estoque="10" data-preco="R$ 25,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Condicionador" data-categoria="Condicionadores" data-estoque="10" data-preco="25.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Condicionador"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td><span class="client-name">Navalha Profissional</span></td>
              <td>Ferramentas</td>
              <td><span class="badge badge--low-stock">4 un</span></td>
              <td><span class="preco-badge">R$ 120,00</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action btn-action--view" title="Ver" data-modal="modal-produto-ver" data-nome="Navalha Profissional" data-categoria="Ferramentas" data-estoque="4" data-preco="R$ 120,00" data-descricao=""><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-produto" data-nome="Navalha Profissional" data-categoria="Ferramentas" data-estoque="4" data-preco="120.00" data-descricao=""><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-produto-excluir" data-nome="Navalha Profissional"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

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
          <label class="modal-label">Nome do Produto</label>
          <input class="modal-input" type="text" data-field="nome" placeholder="Ex: Pomada Matte" />
        </div>
        <div class="modal-field">
          <label class="modal-label">Categoria</label>
          <select class="modal-select" data-field="categoria">
            <option value="">Selecione uma categoria</option>
            <option>Finalizadores</option>
            <option>Shampoos</option>
            <option>Condicionadores</option>
            <option>Barba</option>
            <option>Ferramentas</option>
            <option>Outros</option>
          </select>
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
        <dt>Categoria</dt>
        <dd data-field="categoria">—</dd>
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

<?php include __DIR__ . '/../partials/scripts.php'; ?>
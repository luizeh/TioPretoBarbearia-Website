<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';
require_once __DIR__ . '/../../sql/PedidosSql.php';
$activePage = 'produtos';
$pageTitle = 'Pedidos';
$pedidos = PedidosSql::listarTodos();
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/sidebar.php';
?>
<div class="main-wrapper">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>
    <main class="page-content">
        <div class="page-header">
            <div><p class="page-eyebrow">Vendas</p><h1 class="page-title page-title--lg">Pedidos</h1></div>
        </div>
        <div class="view-toggle">
            <a class="view-toggle__btn" href="produtos.php"><i class="fa-solid fa-box-open"></i> Produtos</a>
            <a class="view-toggle__btn active" href="pedidos.php"><i class="fa-solid fa-receipt"></i> Pedidos</a>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title"><i class="fa-solid fa-receipt"></i> Pedidos dos clientes</h2>
                <div class="table-search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input class="table-search" type="text" placeholder="Pesquisar pedido..." data-search="tbl-pedidos" /></div>
            </div>
            <p class="table-scroll-hint"><i class="fa-solid fa-arrows-left-right"></i> Arraste a tabela para o lado para ver todas as colunas</p>
            <div class="table-wrapper">
                <table class="dash-table" id="tbl-pedidos">
                    <thead><tr><th>ID</th><th>Cliente</th><th>Telefone</th><th>Produtos</th><th>Endereço</th><th>Total</th><th>Status</th><th>Data</th></tr></thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?= (int) $pedido['id'] ?></td>
                                <td><span class="client-name"><?= htmlspecialchars($pedido['cliente']) ?></span></td>
                                <td><?= htmlspecialchars($pedido['telefone']) ?></td>
                                <td><?= htmlspecialchars($pedido['itens']) ?></td>
                                <td><?= htmlspecialchars($pedido['endereco']) ?></td>
                                <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                                <td>
                                    <select class="pedido-status-select" data-pedido-id="<?= (int) $pedido['id'] ?>">
                                        <?php foreach (['recebido' => 'Recebido', 'preparando' => 'Preparando', 'pronto' => 'Pronto', 'entregue' => 'Entregue', 'cancelado' => 'Cancelado'] as $valor => $label): ?>
                                            <option value="<?= $valor ?>"<?= $pedido['status'] === $valor ? ' selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><?= htmlspecialchars($pedido['data_fmt']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$pedidos): ?><tr><td colspan="8" class="table-empty-cell table-empty-cell--large">Nenhum pedido encontrado.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php $pageScripts = ['pedidos.js']; include __DIR__ . '/../partials/scripts.php'; ?>

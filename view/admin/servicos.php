<?php
require_once(__DIR__ . '/../../api/auth/require_admin.php');
$activePage = 'servicos';
$pageTitle  = 'Serviços';
include_once(__DIR__ . '/../../controllers/servicos.controller.php');
include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-wrapper">

    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <main class="page-content">

        <!-- Cabeçalho -->
        <div class="page-header">
            <div>
                <p class="page-eyebrow">✦ Catálogo</p>
                <h1 class="page-title page-title--lg">Serviços</h1>
            </div>
            <button class="btn-primary" data-modal="modal-servico">
                <i class="fa-solid fa-plus"></i> Novo Serviço
            </button>
        </div>

        <!-- Banner total -->
        <div class="clientes-stat-banner clientes-stat-banner--full">
            <div class="clientes-stat-banner__icon">
                <i class="fa-solid fa-scissors"></i>
            </div>
            <div class="clientes-stat-banner__info">
                <span class="clientes-stat-banner__count"><?= $totalServicos ?></span>
                <span class="clientes-stat-banner__label">serviços cadastrados</span>
            </div>
        </div>

        <!-- Tabela -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-scissors"></i> Todos os Serviços
                </h2>
                <div class="table-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="table-search" type="text" placeholder="Pesquisar serviço..." data-search="tbl-servicos" />
                </div>
            </div>
            <div class="table-wrapper">
                <table class="dash-table" id="tbl-servicos">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Duração</th>
                            <th>Preço</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicos as $s): ?>
                            <tr>
                                <td><span class="client-name"><?= htmlspecialchars($s['nome']) ?></span></td>
                                <td><?= (int) $s['tempo_estimado'] ?> min</td>
                                <td><span class="preco-badge">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span></td>
                                <td><?= htmlspecialchars(mb_strlen($s['descricao'] ?? '') > 60 ? mb_substr($s['descricao'], 0, 60) . '…' : ($s['descricao'] ?? '—')) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"
                                            data-modal="modal-servico-ver"
                                            data-nome="<?= htmlspecialchars($s['nome']) ?>"
                                            data-duracao="<?= (int) $s['tempo_estimado'] ?>"
                                            data-preco="R$ <?= number_format($s['preco'], 2, ',', '.') ?>"
                                            data-descricao="<?= htmlspecialchars($s['descricao'] ?? '') ?>"
                                            data-foto_url="<?= htmlspecialchars($s['foto_url'] ?? '') ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-action--edit" title="Editar"
                                            data-modal="modal-servico"
                                            data-id="<?= $s['id'] ?>"
                                            data-nome="<?= htmlspecialchars($s['nome']) ?>"
                                            data-duracao="<?= (int) $s['tempo_estimado'] ?>"
                                            data-preco="<?= $s['preco'] ?>"
                                            data-descricao="<?= htmlspecialchars($s['descricao'] ?? '') ?>"
                                            data-foto_url="<?= htmlspecialchars($s['foto_url'] ?? '') ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn-action btn-action--delete" title="Excluir"
                                            data-modal="modal-servico-excluir"
                                            data-id="<?= $s['id'] ?>"
                                            data-nome="<?= htmlspecialchars($s['nome']) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($servicos)): ?>
                            <tr>
                                <td colspan="5" class="table-empty-cell table-empty-cell--large">Nenhum serviço cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- ── Modais ── -->
<?php
$modal_id         = 'modal-servico';
$modal_use_fields = true;
include __DIR__ . '/../partials/modais/modal-servico.php';
unset($modal_id, $modal_use_fields);

include __DIR__ . '/../partials/modais/modal-servico-ver.php';

$modal_id           = 'modal-servico-excluir';
$modal_title        = 'Excluir Serviço';
$modal_entity_label = 'este serviço';
include __DIR__ . '/../partials/modais/modal-excluir.php';
unset($modal_id, $modal_title, $modal_entity_label);
?>

<?php $pageScripts = ['servicos.js'];
include __DIR__ . '/../partials/scripts.php'; ?>

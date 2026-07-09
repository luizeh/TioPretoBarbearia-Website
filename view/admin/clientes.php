<?php
$activePage = 'clientes';
$pageTitle  = 'Clientes';
include_once(__DIR__ . '/../../controllers/clientes.controller.php');
include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<!-- ══════════════ CONTEÚDO PRINCIPAL ══════════════ -->
<div class="main-wrapper">

    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <!-- PAGE CONTENT -->
    <main class="page-content">

        <!-- Título da página -->
        <div class="page-header">
            <div>
                <p class="page-eyebrow">✦ Gerenciamento</p>
                <h1 class="page-title page-title--lg">Clientes</h1>
            </div>
        </div>

        <!-- Banner total de clientes -->
        <div class="clientes-stat-banner">
            <div class="clientes-stat-banner__icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="clientes-stat-banner__info">
                <span class="clientes-stat-banner__count"><?= count($usuarios) ?></span>
                <span class="clientes-stat-banner__label">clientes cadastrados</span>
            </div>
        </div>

        <!-- ── TABELA DE CLIENTES ── -->
        <div class="dashboard-card clientes-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-users"></i> Todos os Clientes
                </h2>
                <div class="table-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="table-search" type="text" placeholder="Pesquisar clientes..." data-search="tbl-clientes" />
                </div>
            </div>

            <div class="table-wrapper">
                <table class="dash-table" id="tbl-clientes">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><span class="client-name"><?= htmlspecialchars($u['nome']) . ' ' . htmlspecialchars($u['sobrenome']) ?></span></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['telefone']) ?></td>
                                <td><?= htmlspecialchars($u['cidade']) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"
                                            data-modal="modal-cliente-ver"
                                            data-nome="<?= htmlspecialchars($u['nome'] . ' ' . $u['sobrenome']) ?>"
                                            data-email="<?= htmlspecialchars($u['email']) ?>"
                                            data-telefone="<?= htmlspecialchars($u['telefone'] ?? '') ?>"
                                            data-cidade="<?= htmlspecialchars($u['cidade'] ?? '') ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-action--edit" title="Editar"
                                            data-modal="modal-cliente-editar"
                                            data-id="<?= $u['id'] ?>"
                                            data-nome="<?= htmlspecialchars($u['nome']) ?>"
                                            data-sobrenome="<?= htmlspecialchars($u['sobrenome']) ?>"
                                            data-email="<?= htmlspecialchars($u['email']) ?>"
                                            data-telefone="<?= htmlspecialchars($u['telefone'] ?? '') ?>"
                                            data-cidade="<?= htmlspecialchars($u['cidade'] ?? '') ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn-action btn-action--delete" title="Excluir"
                                            data-modal="modal-cliente-excluir"
                                            data-id="<?= $u['id'] ?>"
                                            data-nome="<?= htmlspecialchars($u['nome'] . ' ' . $u['sobrenome']) ?>">
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
include __DIR__ . '/../partials/modais/modal-cliente-ver.php';

$modal_id           = 'modal-cliente-editar';
$modal_title        = 'Editar Cliente';
$modal_confirm_text = 'Salvar';
$modal_use_fields   = true;
include __DIR__ . '/../partials/modais/modal-cliente-form.php';
unset($modal_id, $modal_title, $modal_confirm_text, $modal_use_fields);

$modal_id           = 'modal-cliente-excluir';
$modal_title        = 'Excluir Cliente';
$modal_entity_label = 'este cliente';
include __DIR__ . '/../partials/modais/modal-excluir.php';
unset($modal_id, $modal_title, $modal_entity_label);
?>

<?php $pageScripts = ['clientes.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
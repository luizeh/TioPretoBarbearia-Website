<?php
require_once(__DIR__ . '/../../api/auth/require_admin.php');
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
            <button class="btn-primary" data-modal="modal-cliente-criar">
                <i class="fa-solid fa-plus"></i> Novo Cliente
            </button>
        </div>

        <!-- Banner total de clientes -->
        <div class="clientes-stat-banner">
            <div class="clientes-stat-banner__icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="clientes-stat-banner__info">
                <span class="clientes-stat-banner__count"><?= (int) $totalUsuarios ?></span>
                <span class="clientes-stat-banner__label">usuários cadastrados</span>
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

            <p class="table-scroll-hint"><i class="fa-solid fa-arrows-left-right"></i> Arraste a tabela para o lado para ver todas as colunas</p>
            <div class="table-wrapper">
                <table class="dash-table" id="tbl-clientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Quem promoveu o admin atual (não pode ser alterado por ele).
                        $meuPromotor = (int) ($usuario['promovido_por'] ?? 0);
                        foreach ($usuarios as $u):
                            $ehAdmin    = !empty($u['admin']);
                            $ehPromotor = ($meuPromotor > 0 && (int) $u['id'] === $meuPromotor);
                        ?>
                            <tr>
                                <td>#<?= (int) $u['id'] ?></td>
                                <td><span class="client-name"><?= htmlspecialchars($u['nome']) . ' ' . htmlspecialchars($u['sobrenome']) ?></span></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['telefone']) ?></td>
                                <td><?= htmlspecialchars($u['cidade']) ?></td>
                                <td><span class="badge <?= $ehAdmin ? 'badge--admin' : 'badge--user' ?>"><?= $ehAdmin ? 'Admin' : 'Usuário' ?></span></td>
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
                                        <?php if (!$ehPromotor): ?>
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
                                        <?php endif; ?>
                                        <?php if (!$ehAdmin): ?>
                                            <button class="btn-action btn-action--promote" title="Promover a admin"
                                                data-action-promover
                                                data-id="<?= $u['id'] ?>"
                                                data-nome="<?= htmlspecialchars($u['nome'] . ' ' . $u['sobrenome']) ?>">
                                                <i class="fa-solid fa-user-shield"></i>
                                            </button>
                                            <button class="btn-action btn-action--delete" title="Excluir"
                                                data-modal="modal-cliente-excluir"
                                                data-id="<?= $u['id'] ?>"
                                                data-nome="<?= htmlspecialchars($u['nome'] . ' ' . $u['sobrenome']) ?>">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($ehPromotor): ?>
                                            <span class="badge badge--admin" title="Foi quem promoveu você — não pode ser alterado">Seu promotor</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <?php include __DIR__ . '/../partials/pagination.php'; ?>

        </div>

    </main>
</div>

<!-- ── Modais ── -->
<?php
include __DIR__ . '/../partials/modais/modal-cliente-ver.php';

$modal_id           = 'modal-cliente-criar';
$modal_title        = 'Novo Cliente';
$modal_confirm_text = 'Cadastrar';
$modal_use_fields   = true;
$modal_show_senha   = true;
include __DIR__ . '/../partials/modais/modal-cliente-form.php';
unset($modal_id, $modal_title, $modal_confirm_text, $modal_use_fields, $modal_show_senha);

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
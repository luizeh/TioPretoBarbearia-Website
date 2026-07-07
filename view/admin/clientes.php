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
                                            data-nome="<?= htmlspecialchars($u['nome']) ?>"
                                            data-sobrenome="<?= htmlspecialchars($u['sobrenome']) ?>"
                                            data-email="<?= htmlspecialchars($u['email']) ?>"
                                            data-telefone="<?= htmlspecialchars($u['telefone'] ?? '') ?>"
                                            data-cidade="<?= htmlspecialchars($u['cidade'] ?? '') ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn-action btn-action--delete" title="Excluir"
                                            data-modal="modal-cliente-excluir"
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

<!-- ── Modal: Ver Cliente ── -->
<div class="modal-overlay" id="modal-cliente-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-user"></i> Dados do Cliente</h2>
            <button class="modal-close" data-close="modal-cliente-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Nome</dt>
                <dd data-field="nome">—</dd>
                <dt>E-mail</dt>
                <dd data-field="email">—</dd>
                <dt>Telefone</dt>
                <dd data-field="telefone">—</dd>
                <dt>Cidade</dt>
                <dd data-field="cidade">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cliente-ver">Fechar</button>
        </div>
    </div>
</div>

<!-- ── Modal: Editar Cliente ── -->
<div class="modal-overlay" id="modal-cliente-editar">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-pen"></i> Editar Cliente</h2>
            <button class="modal-close" data-close="modal-cliente-editar"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Nome</label>
                        <input class="modal-input" type="text" data-field="nome" placeholder="Nome" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Sobrenome</label>
                        <input class="modal-input" type="text" data-field="sobrenome" placeholder="Sobrenome" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">E-mail</label>
                    <input class="modal-input" type="email" data-field="email" placeholder="email@exemplo.com" />
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Telefone</label>
                        <input class="modal-input" type="tel" data-field="telefone" placeholder="+55 (00) 00000-0000" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Cidade</label>
                        <input class="modal-input" type="text" data-field="cidade" placeholder="Cidade" />
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cliente-editar">Cancelar</button>
            <button class="btn-modal-primary">Salvar</button>
        </div>
    </div>
</div>

<!-- ── Modal: Excluir Cliente ── -->
<div class="modal-overlay" id="modal-cliente-excluir">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-trash"></i> Excluir Cliente</h2>
            <button class="modal-close" data-close="modal-cliente-excluir"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-delete-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p>Tem certeza que deseja excluir <strong data-field="nome">este cliente</strong>?<br />
                    Esta ação não pode ser desfeita.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cliente-excluir">Cancelar</button>
            <button class="btn-modal-danger">Excluir</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/scripts.php'; ?>
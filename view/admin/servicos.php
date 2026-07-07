<?php
$activePage = 'servicos';
$pageTitle  = 'Serviços';
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
                <p class="page-eyebrow">✦ Catálogo</p>
                <h1 class="page-title page-title--lg">Serviços</h1>
            </div>
            <button class="btn-primary" data-modal="modal-servico">
                <i class="fa-solid fa-plus"></i> Novo Serviço
            </button>
        </div>

        <!-- Banner total -->
        <div class="clientes-stat-banner" style="max-width:100%;">
            <div class="clientes-stat-banner__icon">
                <i class="fa-solid fa-scissors"></i>
            </div>
            <div class="clientes-stat-banner__info">
                <span class="clientes-stat-banner__count">5</span>
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
                        <tr>
                            <td><span class="client-name">Corte Social</span></td>
                            <td>30 min</td>
                            <td><span class="preco-badge">R$ 35,00</span></td>
                            <td>Corte clássico com acabamento perfeito.</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action--view" title="Ver" data-modal="modal-servico-ver" data-nome="Corte Social" data-duracao="30" data-preco="R$ 35,00" data-descricao="Corte clássico com acabamento perfeito."><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-servico" data-nome="Corte Social" data-duracao="30" data-preco="35.00" data-descricao="Corte clássico com acabamento perfeito."><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-servico-excluir" data-nome="Corte Social"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Corte + Barba</span></td>
                            <td>60 min</td>
                            <td><span class="preco-badge">R$ 55,00</span></td>
                            <td>Corte e modelagem completa da barba.</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action--view" title="Ver" data-modal="modal-servico-ver" data-nome="Corte + Barba" data-duracao="60" data-preco="R$ 55,00" data-descricao="Corte e modelagem completa da barba."><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-servico" data-nome="Corte + Barba" data-duracao="60" data-preco="55.00" data-descricao="Corte e modelagem completa da barba."><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-servico-excluir" data-nome="Corte + Barba"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Barba Degradê</span></td>
                            <td>45 min</td>
                            <td><span class="preco-badge">R$ 40,00</span></td>
                            <td>Modelagem com efeito degradê nas laterais.</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action--view" title="Ver" data-modal="modal-servico-ver" data-nome="Barba Degradê" data-duracao="45" data-preco="R$ 40,00" data-descricao="Modelagem com efeito degradê nas laterais."><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-servico" data-nome="Barba Degradê" data-duracao="45" data-preco="40.00" data-descricao="Modelagem com efeito degradê nas laterais."><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-servico-excluir" data-nome="Barba Degradê"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Hidratação</span></td>
                            <td>40 min</td>
                            <td><span class="preco-badge">R$ 45,00</span></td>
                            <td>Tratamento profundo de hidratação capilar.</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action--view" title="Ver" data-modal="modal-servico-ver" data-nome="Hidratação" data-duracao="40" data-preco="R$ 45,00" data-descricao="Tratamento profundo de hidratação capilar."><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-servico" data-nome="Hidratação" data-duracao="40" data-preco="45.00" data-descricao="Tratamento profundo de hidratação capilar."><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-servico-excluir" data-nome="Hidratação"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Sobrancelha</span></td>
                            <td>15 min</td>
                            <td><span class="preco-badge">R$ 20,00</span></td>
                            <td>Design e alinhamento de sobrancelha masculina.</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action--view" title="Ver" data-modal="modal-servico-ver" data-nome="Sobrancelha" data-duracao="15" data-preco="R$ 20,00" data-descricao="Design e alinhamento de sobrancelha masculina."><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-action btn-action--edit" title="Editar" data-modal="modal-servico" data-nome="Sobrancelha" data-duracao="15" data-preco="20.00" data-descricao="Design e alinhamento de sobrancelha masculina."><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir" data-modal="modal-servico-excluir" data-nome="Sobrancelha"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- Modal: Novo / Editar Serviço -->
<div class="modal-overlay" id="modal-servico">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-scissors"></i> Serviço</h2>
            <button class="modal-close" data-close="modal-servico"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Nome do Serviço</label>
                    <input class="modal-input" type="text" data-field="nome" placeholder="Ex: Corte Social" />
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Duração (min)</label>
                        <input class="modal-input" type="number" data-field="duracao" placeholder="30" min="5" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Preço (R$)</label>
                        <input class="modal-input" type="number" data-field="preco" placeholder="35.00" step="0.01" min="0" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Descrição</label>
                    <textarea class="modal-textarea" data-field="descricao" placeholder="Descreva o serviço..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-servico">Cancelar</button>
            <button class="btn-modal-primary">Salvar</button>
        </div>
    </div>
</div>

<!-- ── Modal: Ver Serviço ── -->
<div class="modal-overlay" id="modal-servico-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-scissors"></i> Detalhes do Serviço</h2>
            <button class="modal-close" data-close="modal-servico-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Serviço</dt>
                <dd data-field="nome">—</dd>
                <dt>Duração</dt>
                <dd data-field="duracao">—</dd>
                <dt>Preço</dt>
                <dd data-field="preco">—</dd>
                <dt>Descrição</dt>
                <dd data-field="descricao">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-servico-ver">Fechar</button>
        </div>
    </div>
</div>

<!-- ── Modal: Excluir Serviço ── -->
<div class="modal-overlay" id="modal-servico-excluir">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-trash"></i> Excluir Serviço</h2>
            <button class="modal-close" data-close="modal-servico-excluir"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-delete-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p>Tem certeza que deseja excluir <strong data-field="nome">este serviço</strong>?<br />
                    Esta ação não pode ser desfeita.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-servico-excluir">Cancelar</button>
            <button class="btn-modal-danger">Excluir</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/scripts.php'; ?>
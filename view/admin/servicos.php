<?php
$activePage = 'servicos';
$pageTitle  = 'Serviços';
include_once(__DIR__ . '/../../api/auth/session.php');
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

<?php include __DIR__ . '/../partials/scripts.php'; ?>
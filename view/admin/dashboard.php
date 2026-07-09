<?php
$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
include_once(__DIR__ . '/../../controllers/dashboard.controller.php');
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
                <p class="page-eyebrow">✦ Visão Geral</p>
                <h1 class="page-title">Dashboard</h1>
            </div>
        </div>

        <!-- ── CARDS DE ESTATÍSTICAS ── -->
        <section class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon stat-icon--gold">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Clientes Cadastrados</span>
                    <span class="stat-value" id="statClientes">-</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +12%
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--blue">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Agendamentos Hoje</span>
                    <span class="stat-value" id="statAgendamentos">—</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +5
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--green">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Receita do Mês</span>
                    <span class="stat-value" id="statReceita">—</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +8%
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--orange">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Novos este Mês</span>
                    <span class="stat-value" id="statNovos">—</span>
                </div>
                <div class="stat-trend stat-trend--down">
                    <i class="fa-solid fa-arrow-trend-down"></i> −3%
                </div>
            </div>

        </section>

        <!-- ── LINHA COM TABELA + AÇÕES RÁPIDAS ── -->
        <section class="dashboard-row">

            <!-- Próximos Agendamentos -->
            <div class="dashboard-card dashboard-card--wide">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">
                        <i class="fa-solid fa-calendar-check"></i> Próximos Agendamentos
                    </h2>
                    <div class="table-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input class="table-search" type="text" placeholder="Pesquisar..." data-search="tbl-agendamentos" />
                    </div>
                    <a href="#" class="card-link">Ver todos</a>
                </div>
                <div class="table-wrapper">
                    <table class="dash-table" id="tbl-agendamentos">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Horário</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="client-name">Carlos Mendes</span></td>
                                <td>Corte + Barba</td>
                                <td>09:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Rafael Silva</span></td>
                                <td>Corte Social</td>
                                <td>10:30</td>
                                <td><span class="badge badge--pending">Pendente</span></td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Diego Souza</span></td>
                                <td>Barba Degradê</td>
                                <td>11:15</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Thiago Lima</span></td>
                                <td>Corte + Barba</td>
                                <td>14:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Marcos Ferreira</span></td>
                                <td>Hidratação</td>
                                <td>15:30</td>
                                <td><span class="badge badge--cancelled">Cancelado</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Ações Rápidas -->
            <div class="dashboard-card dashboard-card--narrow">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">
                        <i class="fa-solid fa-bolt"></i> Ações Rápidas
                    </h2>
                </div>
                <div class="quick-actions">
                    <a href="#" class="quick-action-btn" data-modal="modal-agendamento">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Novo Agendamento</span>
                    </a>
                    <a href="#" class="quick-action-btn" data-modal="modal-cliente">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Cadastrar Cliente</span>
                    </a>
                    <a href="#" class="quick-action-btn" data-modal="modal-servico">
                        <i class="fa-solid fa-scissors"></i>
                        <span>Novo Serviço</span>
                    </a>
                    <a href="#" class="quick-action-btn" data-modal="modal-produto">
                        <i class="fa-solid fa-box-open"></i>
                        <span>Adicionar Produto</span>
                    </a>
                    <a href="#" class="quick-action-btn" data-modal="modal-relatorio">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Gerar Relatório</span>
                    </a>
                </div>
            </div>

        </section>

    </main>
</div>

<!-- ══════════════ MODAIS ══════════════ -->
<?php
$modal_confirm_text = 'Agendar';
include __DIR__ . '/../partials/modais/modal-agendamento.php';
unset($modal_confirm_text);

$modal_id           = 'modal-cliente';
$modal_title        = 'Cadastrar Cliente';
$modal_confirm_text = 'Cadastrar';
$modal_use_fields   = false;
include __DIR__ . '/../partials/modais/modal-cliente-form.php';
unset($modal_id, $modal_title, $modal_confirm_text, $modal_use_fields);

$modal_id           = 'modal-servico';
$modal_use_fields   = false;
include __DIR__ . '/../partials/modais/modal-servico.php';
unset($modal_id, $modal_use_fields);

include __DIR__ . '/../partials/modais/modal-produto-rapido.php';
include __DIR__ . '/../partials/modais/modal-relatorio.php';
?>

<?php include __DIR__ . '/../partials/scripts.php'; ?>
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
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--orange">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Novos este Mês</span>
                    <span class="stat-value" id="statNovos">—</span>
                </div>
            </div>

        </section>

        <!-- ── PRÓXIMOS AGENDAMENTOS (full-width) ── -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-calendar-check"></i> Próximos Agendamentos
                </h2>
                <div class="table-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="table-search" type="text" placeholder="Pesquisar..." data-search="tbl-agendamentos" />
                </div>
                <a href="agendamentos.php" class="card-link">Ver todos</a>
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
                        <?php foreach ($proximosAgendamentos as $ag): ?>
                            <tr>
                                <td><span class="client-name"><?= htmlspecialchars($ag['cliente']) ?></span></td>
                                <td><?= htmlspecialchars($ag['servico']) ?></td>
                                <td><?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?></td>
                                <td>
                                    <?php
                                    $b = ['pendente' => 'badge--pending', 'confirmado' => 'badge--confirmed', 'cancelado' => 'badge--cancelled', 'finalizado' => 'badge--finalizado'];
                                    $bc = $b[$ag['status']] ?? 'badge--pending';
                                    ?>
                                    <span class="badge <?= $bc ?>"><?= ucfirst($ag['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($proximosAgendamentos)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;padding:24px;opacity:.5;">Nenhum agendamento próximo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

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

<?php $pageScripts = ['dashboard-page.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
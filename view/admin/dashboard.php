<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';

$activePage = 'dashboard';
$pageTitle = 'Dashboard';

include_once __DIR__ . '/../../controllers/dashboard.controller.php';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="main-wrapper">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <main class="page-content">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Visão Geral</p>
                <h1 class="page-title">Dashboard</h1>
            </div>
        </div>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon--gold"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Clientes Cadastrados</span>
                    <span class="stat-value" id="statClientes">-</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-calendar-day"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Agendamentos Hoje</span>
                    <span class="stat-value" id="statAgendamentos">-</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--orange"><i class="fa-solid fa-user-plus"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Novos este Mês</span>
                    <span class="stat-value" id="statNovos">-</span>
                </div>
            </div>
        </section>

        <section class="dashboard-card dashboard-logs-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title"><i class="fa-solid fa-clock-rotate-left"></i> Logs do sistema</h2>
                <div class="table-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="table-search" type="search" placeholder="Pesquisar..." data-search="tbl-logs" />
                </div>
            </div>
            <div class="table-wrapper">
                <table class="dash-table" id="tbl-logs">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Descrição</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= (int) $log['id'] ?></td>
                                <td><?= (int) $log['usuario_id'] ?></td>
                                <td><?= htmlspecialchars($log['acao']) ?></td>
                                <td><?= htmlspecialchars($log['descricao'] ?? 'Nenhuma') ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($log['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="5" class="table-empty-cell">Nenhum log registrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<?php
$pageScripts = ['dashboard.js'];
include __DIR__ . '/../partials/scripts.php';
?>

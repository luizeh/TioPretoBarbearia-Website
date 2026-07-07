<?php
$activePage = 'relatorios';
$pageTitle  = 'Relatórios';
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
                <p class="page-eyebrow">✦ Análise</p>
                <h1 class="page-title page-title--lg">Relatórios</h1>
            </div>
        </div>

        <!-- Stats -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon--blue">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Atendimentos no Mês</span>
                    <span class="stat-value">47</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +9%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--green">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Receita do Mês</span>
                    <span class="stat-value">R$ 2.385</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +14%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--gold">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Ticket Médio</span>
                    <span class="stat-value">R$ 50,74</span>
                </div>
                <div class="stat-trend stat-trend--up">
                    <i class="fa-solid fa-arrow-trend-up"></i> +4%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--orange">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Novos Clientes</span>
                    <span class="stat-value">12</span>
                </div>
                <div class="stat-trend stat-trend--down">
                    <i class="fa-solid fa-arrow-trend-down"></i> −3%
                </div>
            </div>
        </section>

        <!-- Gráficos -->
        <div class="report-grid">

            <!-- Receita por Mês -->
            <div class="chart-card">
                <h3 class="chart-card-title">
                    <i class="fa-solid fa-chart-column"></i> Receita por Mês
                </h3>
                <div class="bar-chart">
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 45%"></div>
                        </div>
                        <span class="bar-chart__label">Fev</span>
                        <span class="bar-chart__value">R$1.8k</span>
                    </div>
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 52%"></div>
                        </div>
                        <span class="bar-chart__label">Mar</span>
                        <span class="bar-chart__value">R$2.1k</span>
                    </div>
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 47%"></div>
                        </div>
                        <span class="bar-chart__label">Abr</span>
                        <span class="bar-chart__value">R$1.9k</span>
                    </div>
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 60%"></div>
                        </div>
                        <span class="bar-chart__label">Mai</span>
                        <span class="bar-chart__value">R$2.4k</span>
                    </div>
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 70%"></div>
                        </div>
                        <span class="bar-chart__label">Jun</span>
                        <span class="bar-chart__value">R$2.8k</span>
                    </div>
                    <div class="bar-chart__item">
                        <div class="bar-chart__bar-wrap">
                            <div class="bar-chart__bar" style="--h: 60%; background: var(--gold-light);"></div>
                        </div>
                        <span class="bar-chart__label">Jul</span>
                        <span class="bar-chart__value">R$2.4k</span>
                    </div>
                </div>
            </div>

            <!-- Serviços mais realizados -->
            <div class="chart-card">
                <h3 class="chart-card-title">
                    <i class="fa-solid fa-scissors"></i> Serviços Mais Realizados
                </h3>
                <div class="service-breakdown">
                    <div class="service-breakdown__item">
                        <div class="service-breakdown__header">
                            <span class="service-breakdown__name">Corte + Barba</span>
                            <span class="service-breakdown__pct">38%</span>
                        </div>
                        <div class="service-breakdown__track">
                            <div class="service-breakdown__bar" style="--w: 38%"></div>
                        </div>
                    </div>
                    <div class="service-breakdown__item">
                        <div class="service-breakdown__header">
                            <span class="service-breakdown__name">Corte Social</span>
                            <span class="service-breakdown__pct">28%</span>
                        </div>
                        <div class="service-breakdown__track">
                            <div class="service-breakdown__bar" style="--w: 28%"></div>
                        </div>
                    </div>
                    <div class="service-breakdown__item">
                        <div class="service-breakdown__header">
                            <span class="service-breakdown__name">Barba Degradê</span>
                            <span class="service-breakdown__pct">20%</span>
                        </div>
                        <div class="service-breakdown__track">
                            <div class="service-breakdown__bar" style="--w: 20%"></div>
                        </div>
                    </div>
                    <div class="service-breakdown__item">
                        <div class="service-breakdown__header">
                            <span class="service-breakdown__name">Hidratação</span>
                            <span class="service-breakdown__pct">10%</span>
                        </div>
                        <div class="service-breakdown__track">
                            <div class="service-breakdown__bar" style="--w: 10%"></div>
                        </div>
                    </div>
                    <div class="service-breakdown__item">
                        <div class="service-breakdown__header">
                            <span class="service-breakdown__name">Sobrancelha</span>
                            <span class="service-breakdown__pct">4%</span>
                        </div>
                        <div class="service-breakdown__track">
                            <div class="service-breakdown__bar" style="--w: 4%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Últimos Atendimentos -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-clock-rotate-left"></i> Últimos Atendimentos
                </h2>
            </div>
            <div class="table-wrapper">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="client-name">Lucas Ramos</span></td>
                            <td>Corte + Barba</td>
                            <td>11/07/2026</td>
                            <td><span class="preco-badge">R$ 55,00</span></td>
                            <td><span class="badge badge--confirmed">Confirmado</span></td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Thiago Lima</span></td>
                            <td>Corte Social</td>
                            <td>10/07/2026</td>
                            <td><span class="preco-badge">R$ 35,00</span></td>
                            <td><span class="badge badge--confirmed">Confirmado</span></td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Diego Souza</span></td>
                            <td>Corte + Barba</td>
                            <td>09/07/2026</td>
                            <td><span class="preco-badge">R$ 55,00</span></td>
                            <td><span class="badge badge--confirmed">Confirmado</span></td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Marcos Ferreira</span></td>
                            <td>Corte Social</td>
                            <td>09/07/2026</td>
                            <td><span class="preco-badge">R$ 35,00</span></td>
                            <td><span class="badge badge--cancelled">Cancelado</span></td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Rafael Silva</span></td>
                            <td>Barba Degradê</td>
                            <td>08/07/2026</td>
                            <td><span class="preco-badge">R$ 40,00</span></td>
                            <td><span class="badge badge--pending">Pendente</span></td>
                        </tr>
                        <tr>
                            <td><span class="client-name">Carlos Mendes</span></td>
                            <td>Corte Social</td>
                            <td>07/07/2026</td>
                            <td><span class="preco-badge">R$ 35,00</span></td>
                            <td><span class="badge badge--confirmed">Confirmado</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<?php include __DIR__ . '/../partials/scripts.php'; ?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../img/favicon.png" type="image/png" />
    <title>Dashboard — Tio Preto Barbearia</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/dashboard.css" />
</head>

<body>

    <!-- ══════════════ SIDEBAR ══════════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="../img/tiopretonb.png" alt="Tio Preto Barbearia" />
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-users"></i>
                <span>Clientes</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Agendamentos</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-scissors"></i>
                <span>Serviços</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-box-open"></i>
                <span>Produtos</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-chart-line"></i>
                <span>Relatórios</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="#" class="nav-item nav-item--settings">
                <i class="fa-solid fa-gear"></i>
                <span>Configurações</span>
            </a>
            <a href="login.php" class="nav-item nav-item--logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair</span>
            </a>
        </div>
    </aside>

    <!-- ══════════════ CONTEÚDO PRINCIPAL ══════════════ -->
    <div class="main-wrapper">

        <!-- TOP BAR -->
        <header class="topbar">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-greeting">
                <span class="topbar-date" id="topbarDate"></span>
            </div>
            <div class="topbar-user">
                <span class="topbar-user-name">Administrador</span>
                <div class="topbar-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>

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
                        <span class="stat-value" id="statClientes">—</span>
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
                        <a href="#" class="card-link">Ver todos</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="dash-table">
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
                        <a href="#" class="quick-action-btn">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Novo Agendamento</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Cadastrar Cliente</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fa-solid fa-scissors"></i>
                            <span>Novo Serviço</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fa-solid fa-box-open"></i>
                            <span>Adicionar Produto</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Gerar Relatório</span>
                        </a>
                    </div>
                </div>

            </section>

        </main>
    </div>

    <script>
        // Data no topbar
        const d = new Date();
        const opts = {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        };
        document.getElementById('topbarDate').textContent =
            d.toLocaleDateString('pt-BR', opts)
            .replace(/^\w/, c => c.toUpperCase());

        // Toggle sidebar mobile
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('sidebar--open');
        });

        // Placeholders de stats (substituir por fetch real futuramente)
        document.getElementById('statClientes').textContent = '87';
        document.getElementById('statAgendamentos').textContent = '12';
        document.getElementById('statReceita').textContent = 'R$ 4.380';
        document.getElementById('statNovos').textContent = '9';
    </script>
</body>

</html>
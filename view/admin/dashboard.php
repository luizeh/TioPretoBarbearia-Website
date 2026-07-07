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

<!-- Modal: Novo Agendamento -->
<div class="modal-overlay" id="modal-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Novo Agendamento</h2>
            <button class="modal-close" data-close="modal-agendamento"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Cliente</label>
                    <input class="modal-input" type="text" placeholder="Nome do cliente" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Serviço</label>
                    <select class="modal-select">
                        <option value="">Selecione um serviço</option>
                        <option>Corte Social</option>
                        <option>Corte + Barba</option>
                        <option>Barba Degradê</option>
                        <option>Hidratação</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data</label>
                        <input class="modal-input" type="date" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Horário</label>
                        <input class="modal-input" type="time" />
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-agendamento">Cancelar</button>
            <button class="btn-modal-primary">Agendar</button>
        </div>
    </div>
</div>

<!-- Modal: Cadastrar Cliente -->
<div class="modal-overlay" id="modal-cliente">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-user-plus"></i> Cadastrar Cliente</h2>
            <button class="modal-close" data-close="modal-cliente"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Nome</label>
                        <input class="modal-input" type="text" placeholder="Nome" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Sobrenome</label>
                        <input class="modal-input" type="text" placeholder="Sobrenome" />
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Telefone</label>
                        <input class="modal-input" type="tel" placeholder="(00) 9 0000-0000" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Cidade</label>
                        <input class="modal-input" type="text" placeholder="Cidade" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">E-mail</label>
                    <input class="modal-input" type="email" placeholder="seu@email.com" />
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cliente">Cancelar</button>
            <button class="btn-modal-primary">Cadastrar</button>
        </div>
    </div>
</div>

<!-- Modal: Novo Serviço -->
<div class="modal-overlay" id="modal-servico">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-scissors"></i> Novo Serviço</h2>
            <button class="modal-close" data-close="modal-servico"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Nome do Serviço</label>
                    <input class="modal-input" type="text" placeholder="Ex: Corte Social" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Descrição</label>
                    <textarea class="modal-textarea" placeholder="Descreva o serviço..."></textarea>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Preço (R$)</label>
                        <input class="modal-input" type="number" min="0" step="0.01" placeholder="0,00" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Duração (min)</label>
                        <input class="modal-input" type="number" min="5" step="5" placeholder="30" />
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-servico">Cancelar</button>
            <button class="btn-modal-primary">Salvar</button>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Produto -->
<div class="modal-overlay" id="modal-produto">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Adicionar Produto</h2>
            <button class="modal-close" data-close="modal-produto"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Nome do Produto</label>
                    <input class="modal-input" type="text" placeholder="Ex: Pomada Modeladora" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Categoria</label>
                    <select class="modal-select">
                        <option value="">Selecione uma categoria</option>
                        <option>Finalizador</option>
                        <option>Shampoo</option>
                        <option>Condicionador</option>
                        <option>Óleo para Barba</option>
                        <option>Outros</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Preço (R$)</label>
                        <input class="modal-input" type="number" min="0" step="0.01" placeholder="0,00" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Estoque (un)</label>
                        <input class="modal-input" type="number" min="0" placeholder="0" />
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-produto">Cancelar</button>
            <button class="btn-modal-primary">Adicionar</button>
        </div>
    </div>
</div>

<!-- Modal: Gerar Relatório -->
<div class="modal-overlay" id="modal-relatorio">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-file-invoice-dollar"></i> Gerar Relatório</h2>
            <button class="modal-close" data-close="modal-relatorio"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Tipo de Relatório</label>
                    <select class="modal-select">
                        <option value="">Selecione o tipo</option>
                        <option>Agendamentos</option>
                        <option>Receita</option>
                        <option>Clientes</option>
                        <option>Serviços Mais Vendidos</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data Início</label>
                        <input class="modal-input" type="date" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Data Fim</label>
                        <input class="modal-input" type="date" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Formato</label>
                    <select class="modal-select">
                        <option>PDF</option>
                        <option>Excel (.xlsx)</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-relatorio">Cancelar</button>
            <button class="btn-modal-primary">Gerar</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/scripts.php'; ?>
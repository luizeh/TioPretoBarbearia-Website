<?php
$activePage = 'agendamentos';
$pageTitle  = 'Agendamentos';
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
                <p class="page-eyebrow">✦ Gestão</p>
                <h1 class="page-title page-title--lg">Agendamentos</h1>
            </div>
            <button class="btn-primary" data-modal="modal-agendamento">
                <i class="fa-solid fa-plus"></i> Novo Agendamento
            </button>
        </div>

        <!-- Stats -->
        <div class="agenda-stats">
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--blue">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value">4</span>
                    <span class="agenda-stat-card__label">Hoje</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value">6</span>
                    <span class="agenda-stat-card__label">Confirmados</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--amber">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value">2</span>
                    <span class="agenda-stat-card__label">Pendentes</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--red">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value">1</span>
                    <span class="agenda-stat-card__label">Cancelados</span>
                </div>
            </div>
        </div>

        <!-- Toggle de visão -->
        <div class="view-toggle">
            <button class="view-toggle__btn active" id="btn-agenda">
                <i class="fa-solid fa-calendar-week"></i> Agenda
            </button>
            <button class="view-toggle__btn" id="btn-lista">
                <i class="fa-solid fa-list"></i> Lista
            </button>
        </div>

        <!-- ─────────────── VIEW: AGENDA ─────────────── -->
        <div id="view-agenda">
            <div class="agenda-wrap">
                <div class="agenda-nav">
                    <button class="agenda-nav__btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <span class="agenda-nav__label">Semana de 07 – 12 Jul 2026</span>
                    <button class="agenda-nav__btn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="agenda-scroll">
                    <div class="agenda-grid">

                        <!-- Cabeçalho dos dias -->
                        <div class="agenda-corner"></div>
                        <div class="agenda-day-head today">Seg <span>07</span></div>
                        <div class="agenda-day-head">Ter <span>08</span></div>
                        <div class="agenda-day-head">Qua <span>09</span></div>
                        <div class="agenda-day-head">Qui <span>10</span></div>
                        <div class="agenda-day-head">Sex <span>11</span></div>
                        <div class="agenda-day-head">Sáb <span>12</span></div>

                        <!-- 08:00 -->
                        <div class="agenda-hour">08:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 1">
                                <span class="agenda-appt__name">Thiago Lima</span>
                                <span class="agenda-appt__service">Corte Social · 30 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 08:30 -->
                        <div class="agenda-hour agenda-hour--half">08:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 09:00 -->
                        <div class="agenda-hour">09:00</div>
                        <div class="agenda-cell today">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 1">
                                <span class="agenda-appt__name">Carlos Mendes</span>
                                <span class="agenda-appt__service">Corte Social · 30 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 2">
                                <span class="agenda-appt__name">Diego Souza</span>
                                <span class="agenda-appt__service">Corte + Barba · 60 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--pending" style="--slots: 2">
                                <span class="agenda-appt__name">Felipe Nunes</span>
                                <span class="agenda-appt__service">Corte + Barba · 60 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- 09:30 -->
                        <div class="agenda-hour agenda-hour--half">09:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell agenda-cell--cont-confirmed"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell agenda-cell--cont-pending"></div>

                        <!-- 10:00 -->
                        <div class="agenda-hour">10:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--pending" style="--slots: 1.5">
                                <span class="agenda-appt__name">Rafael Silva</span>
                                <span class="agenda-appt__service">Barba Degradê · 45 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 10:30 -->
                        <div class="agenda-hour agenda-hour--half">10:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell agenda-cell--cont-pending"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 11:00 -->
                        <div class="agenda-hour">11:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 2">
                                <span class="agenda-appt__name">Lucas Ramos</span>
                                <span class="agenda-appt__service">Corte + Barba · 60 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>

                        <!-- 11:30 -->
                        <div class="agenda-hour agenda-hour--half">11:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell agenda-cell--cont-confirmed"></div>
                        <div class="agenda-cell"></div>

                        <!-- 12:00 -->
                        <div class="agenda-hour">12:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 12:30 -->
                        <div class="agenda-hour agenda-hour--half">12:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 13:00 -->
                        <div class="agenda-hour">13:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--cancelled" style="--slots: 1">
                                <span class="agenda-appt__name">Marcos Ferreira</span>
                                <span class="agenda-appt__service">Corte Social · 30 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 13:30 -->
                        <div class="agenda-hour agenda-hour--half">13:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 14:00 -->
                        <div class="agenda-hour">14:00</div>
                        <div class="agenda-cell today">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 2">
                                <span class="agenda-appt__name">Pedro Alves</span>
                                <span class="agenda-appt__service">Corte + Barba · 60 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 14:30 -->
                        <div class="agenda-hour agenda-hour--half">14:30</div>
                        <div class="agenda-cell today agenda-cell--cont-confirmed"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 15:00 -->
                        <div class="agenda-hour">15:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 15:30 -->
                        <div class="agenda-hour agenda-hour--half">15:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 16:00 -->
                        <div class="agenda-hour">16:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--pending" style="--slots: 1.5">
                                <span class="agenda-appt__name">Bruno Costa</span>
                                <span class="agenda-appt__service">Barba Degradê · 45 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 16:30 -->
                        <div class="agenda-hour agenda-hour--half">16:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell agenda-cell--cont-pending"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 17:00 -->
                        <div class="agenda-hour">17:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell">
                            <div class="agenda-appt agenda-appt--confirmed" style="--slots: 1">
                                <span class="agenda-appt__name">André Santos</span>
                                <span class="agenda-appt__service">Corte Social · 30 min</span>
                                <div class="agenda-appt__actions">
                                    <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-cell"></div>

                        <!-- 17:30 -->
                        <div class="agenda-hour agenda-hour--half">17:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 18:00 -->
                        <div class="agenda-hour">18:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 18:30 -->
                        <div class="agenda-hour agenda-hour--half">18:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 19:00 -->
                        <div class="agenda-hour">19:00</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                        <!-- 19:30 -->
                        <div class="agenda-hour agenda-hour--half">19:30</div>
                        <div class="agenda-cell today"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>
                        <div class="agenda-cell"></div>

                    </div><!-- /.agenda-grid -->
                </div><!-- /.agenda-scroll -->
            </div><!-- /.agenda-wrap -->
        </div><!-- /#view-agenda -->

        <!-- ─────────────── VIEW: LISTA ─────────────── -->
        <div id="view-lista" style="display:none;">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">
                        <i class="fa-solid fa-calendar-check"></i> Todos os Agendamentos
                    </h2>
                    <div class="table-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input class="table-search" type="text" placeholder="Pesquisar..." data-search="tbl-agendamentos" />
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="dash-table" id="tbl-agendamentos">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="client-name">Carlos Mendes</span></td>
                                <td>Corte Social</td>
                                <td>07/07/2026</td>
                                <td>09:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Pedro Alves</span></td>
                                <td>Corte + Barba</td>
                                <td>07/07/2026</td>
                                <td>14:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Rafael Silva</span></td>
                                <td>Barba Degradê</td>
                                <td>08/07/2026</td>
                                <td>10:00</td>
                                <td><span class="badge badge--pending">Pendente</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Diego Souza</span></td>
                                <td>Corte + Barba</td>
                                <td>09/07/2026</td>
                                <td>09:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Marcos Ferreira</span></td>
                                <td>Corte Social</td>
                                <td>09/07/2026</td>
                                <td>13:00</td>
                                <td><span class="badge badge--cancelled">Cancelado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Thiago Lima</span></td>
                                <td>Corte Social</td>
                                <td>10/07/2026</td>
                                <td>08:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Lucas Ramos</span></td>
                                <td>Corte + Barba</td>
                                <td>11/07/2026</td>
                                <td>11:00</td>
                                <td><span class="badge badge--confirmed">Confirmado</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="client-name">Felipe Nunes</span></td>
                                <td>Corte + Barba</td>
                                <td>12/07/2026</td>
                                <td>09:00</td>
                                <td><span class="badge badge--pending">Pendente</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action--view" title="Ver"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn-action btn-action--edit" data-modal="modal-agendamento" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action btn-action--delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /#view-lista -->

    </main>
</div>

<!-- Modal: Novo / Editar Agendamento -->
<div class="modal-overlay" id="modal-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Agendamento</h2>
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
                        <option>Sobrancelha</option>
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
                <div class="modal-field">
                    <label class="modal-label">Status</label>
                    <select class="modal-select">
                        <option value="confirmado">Confirmado</option>
                        <option value="pendente">Pendente</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-agendamento">Cancelar</button>
            <button class="btn-modal-primary">Salvar</button>
        </div>
    </div>
</div>

<script>
    // Toggle Lista / Agenda
    document.getElementById('btn-agenda').addEventListener('click', function() {
        document.getElementById('view-agenda').style.display = '';
        document.getElementById('view-lista').style.display = 'none';
        this.classList.add('active');
        document.getElementById('btn-lista').classList.remove('active');
    });
    document.getElementById('btn-lista').addEventListener('click', function() {
        document.getElementById('view-lista').style.display = '';
        document.getElementById('view-agenda').style.display = 'none';
        this.classList.add('active');
        document.getElementById('btn-agenda').classList.remove('active');
    });
</script>
<?php include __DIR__ . '/../partials/scripts.php'; ?>
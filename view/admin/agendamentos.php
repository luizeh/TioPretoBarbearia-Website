<?php
$activePage = 'agendamentos';
$pageTitle  = 'Agendamentos';
include_once(__DIR__ . '/../../controllers/agendamentos.controller.php');
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
                    <span class="agenda-stat-card__value"><?= $stats['hoje'] ?></span>
                    <span class="agenda-stat-card__label">Hoje</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value"><?= $stats['confirmados'] ?></span>
                    <span class="agenda-stat-card__label">Confirmados</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--amber">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value"><?= $stats['pendentes'] ?></span>
                    <span class="agenda-stat-card__label">Pendentes</span>
                </div>
            </div>
            <div class="agenda-stat-card">
                <div class="agenda-stat-card__icon agenda-stat-card__icon--red">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="agenda-stat-card__info">
                    <span class="agenda-stat-card__value"><?= $stats['cancelados'] ?></span>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                                    <button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                            <?php
                            $badgeMap = [
                                'pendente'   => 'badge--pending',
                                'confirmado' => 'badge--confirmed',
                                'cancelado'  => 'badge--cancelled',
                                'finalizado' => 'badge--finalizado',
                            ];
                            foreach ($agendamentos as $ag):
                                $badgeClass = $badgeMap[$ag['status']] ?? 'badge--pending';
                                $statusLabel = ucfirst($ag['status']);
                            ?>
                                <tr>
                                    <td><span class="client-name"><?= htmlspecialchars($ag['cliente']) ?></span></td>
                                    <td><?= htmlspecialchars($ag['servico']) ?></td>
                                    <td><?= htmlspecialchars($ag['data_fmt']) ?></td>
                                    <td><?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn-action btn-action--view" title="Ver"
                                                data-modal="modal-agendamento-ver"
                                                data-cliente="<?= htmlspecialchars($ag['cliente']) ?>"
                                                data-servico="<?= htmlspecialchars($ag['servico']) ?>"
                                                data-data="<?= htmlspecialchars($ag['data_fmt']) ?>"
                                                data-horario="<?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?>"
                                                data-status="<?= $statusLabel ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-action--edit" title="Editar"
                                                data-modal="modal-agendamento"
                                                data-id="<?= $ag['id'] ?>"
                                                data-cliente="<?= htmlspecialchars($ag['cliente']) ?>"
                                                data-servico="<?= htmlspecialchars($ag['servico']) ?>"
                                                data-status="<?= $ag['status'] ?>">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button class="btn-action btn-action--whatsapp" title="Lembrete WhatsApp"
                                                data-cliente="<?= htmlspecialchars($ag['cliente']) ?>"
                                                data-telefone="<?= htmlspecialchars($ag['telefone'] ?? '') ?>"
                                                data-servico="<?= htmlspecialchars($ag['servico']) ?>"
                                                data-data="<?= htmlspecialchars($ag['data_fmt']) ?>"
                                                data-hora="<?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?>"
                                                style="color:#25d366">
                                                <i class="fa-brands fa-whatsapp"></i>
                                            </button>
                                            <button class="btn-action btn-action--delete" title="Excluir"
                                                data-modal="modal-agendamento-excluir"
                                                data-id="<?= $ag['id'] ?>"
                                                data-nome="<?= htmlspecialchars($ag['cliente']) ?>">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($agendamentos)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:32px;opacity:.5;">Nenhum agendamento encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /#view-lista -->

    </main>
</div>

<!-- ── Modais ── -->
<?php
$modal_show_status  = true;
include __DIR__ . '/../partials/modais/modal-agendamento.php';
unset($modal_show_status);

include __DIR__ . '/../partials/modais/modal-agendamento-ver.php';

$modal_id           = 'modal-agendamento-excluir';
$modal_title        = 'Excluir Agendamento';
$modal_entity_label = 'este agendamento';
include __DIR__ . '/../partials/modais/modal-excluir.php';
unset($modal_id, $modal_title, $modal_entity_label);
?>

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
<?php $pageScripts = ['agendamentos.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
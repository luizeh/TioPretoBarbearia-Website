<?php
require_once(__DIR__ . '/../../api/auth/require_admin.php');
$activePage = 'agendamentos';
$pageTitle  = 'Agendamentos';
include_once(__DIR__ . '/../../controllers/agendamentos.controller.php');
require_once(__DIR__ . '/../../sql/ClientesSql.php');
$clientes = ClientesSql::listar(200, 0);
$weekStart = isset($_GET['data']) ? strtotime($_GET['data']) : strtotime('today');
$weekStart = strtotime('monday this week', $weekStart);
$weekDays  = [];
for ($i = 0; $i < 6; $i++) {
    $weekDays[] = date('Y-m-d', strtotime('+'.$i.' days', $weekStart));
}
$selectedDate = (isset($_GET['dia']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['dia'])) ? $_GET['dia'] : date('Y-m-d');
$diasSemana = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
$meses = [1 => 'jan', 2 => 'fev', 3 => 'mar', 4 => 'abr', 5 => 'mai', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'set', 10 => 'out', 11 => 'nov', 12 => 'dez'];
$weekLabel = date('d', $weekStart) . ' a ' . date('d', strtotime('+5 days', $weekStart)) . ' de ' . $meses[(int) date('n', strtotime('+5 days', $weekStart))] . ' de ' . date('Y', strtotime('+5 days', $weekStart));
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
                    <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('-7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <span class="agenda-nav__label">Semana de <?= $weekLabel ?></span>
                    <div class="agenda-day-actions">
                        <label for="agenda-lembrete-data">Dia</label>
                        <input type="date" id="agenda-lembrete-data" lang="pt-BR" value="<?= htmlspecialchars($selectedDate) ?>">
                        <button type="button" class="btn-agenda-whatsapp-dia" data-date="<?= htmlspecialchars($selectedDate) ?>" title="Enviar lembrete para todos os clientes do dia">
                            <i class="fa-brands fa-whatsapp"></i> Lembrar clientes
                        </button>
                    </div>
                    <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('+7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
                <p class="agenda-mobile-hint"><i class="fa-solid fa-arrows-left-right"></i> Deslize para ver todos os dias</p>
                <div class="agenda-scroll">
                    <div class="agenda-grid">
                        <div class="agenda-corner"></div>
                        <?php foreach ($weekDays as $index => $day): ?>
                            <div class="agenda-day-head<?= $day === date('Y-m-d') ? ' today' : '' ?>">
                                <?= $diasSemana[$index] ?> <span><?= date('d', strtotime($day)) ?></span>
                            </div>
                        <?php endforeach; ?>

                        <?php
                        $startHour = 8;
                        $endHour   = 19;
                        $slots     = [];
                        for ($hour = $startHour; $hour <= $endHour; $hour++) {
                            foreach ([0, 30] as $minute) {
                                $slots[] = sprintf('%02d:%02d', $hour, $minute);
                            }
                        }
                        foreach ($slots as $slotTime):
                            $isHalf = (int) substr($slotTime, -2) === 30;
                            echo '<div class="agenda-hour' . ($isHalf ? ' agenda-hour--half' : '') . '">' . $slotTime . '</div>';
                            foreach ($weekDays as $day):
                                $cellKey = $day . ' ' . $slotTime;
                                $cellAppointments = $agendaMap[$day][$slotTime] ?? [];
                                $inicioAppointments = array_filter($cellAppointments, static fn(array $appointment): bool => empty($appointment['continuacao']));
                                echo '<div class="agenda-cell' . ($day === date('Y-m-d') ? ' today' : '') . '" data-date="' . $day . '" data-time="' . $slotTime . '">';
                                if (!empty($inicioAppointments)) {
                                    foreach ($inicioAppointments as $appointment):
                                        $durationMinutes = (int) ($appointment['duracao_minutos'] ?? 30);
                                        $slotsSpan = min(24, max(1, (int) ceil($durationMinutes / 30)));
                                        $statusClass = $appointment['status'] === 'confirmado' ? 'agenda-appt--confirmed' : ($appointment['status'] === 'pendente' ? 'agenda-appt--pending' : 'agenda-appt--cancelled');
                                        echo '<div class="agenda-appt ' . $statusClass . ' agenda-appt--slots-' . $slotsSpan . '">';
                                        echo '<span class="agenda-appt__name">' . htmlspecialchars($appointment['cliente']) . '</span>';
                                        echo '<span class="agenda-appt__service">' . htmlspecialchars($appointment['servico']) . ' · ' . (int) ($appointment['duracao_minutos'] ?? 30) . ' min</span>';
                                        echo '<div class="agenda-appt__actions">';
                                        echo '<button class="btn-action btn-action--edit" data-modal="modal-agendamento" data-id="' . (int) $appointment['id'] . '" data-usuario_id="' . (int) $appointment['usuario_id'] . '" data-cliente="' . htmlspecialchars($appointment['cliente']) . '" data-servico_id="' . (int) ($appointment['servico_id'] ?? 0) . '" data-servicos_ids="' . htmlspecialchars($appointment['servicos_ids'] ?? '') . '" data-data="' . htmlspecialchars($appointment['data']) . '" data-hora="' . htmlspecialchars(substr($appointment['hora_inicio'], 0, 5)) . '" data-status="' . htmlspecialchars($appointment['status']) . '" data-observacoes="' . htmlspecialchars($appointment['observacoes'] ?? '') . '" title="Editar"><i class="fa-solid fa-pen"></i></button>';
                                        echo '<button class="btn-action btn-action--whatsapp" data-cliente="' . htmlspecialchars($appointment['cliente']) . '" data-telefone="' . htmlspecialchars($appointment['telefone'] ?? '') . '" data-servico="' . htmlspecialchars($appointment['servico']) . '" data-data="' . htmlspecialchars(date('d/m/Y', strtotime($appointment['data']))) . '" data-hora="' . htmlspecialchars(substr($appointment['hora_inicio'], 0, 5)) . '" title="Enviar lembrete no WhatsApp"><i class="fa-brands fa-whatsapp"></i></button>';
                                        echo '<button class="btn-action btn-action--delete" data-modal="modal-agendamento-excluir" data-id="' . (int) $appointment['id'] . '" data-nome="' . htmlspecialchars($appointment['cliente']) . '" title="Excluir"><i class="fa-solid fa-trash"></i></button>';
                                        echo '</div></div>';
                                    endforeach;
                                } elseif (!empty($cellAppointments)) {
                                    echo '<div class="agenda-cell__blocked" aria-label="Horário ocupado"></div>';
                                } else {
                                    echo '<button class="agenda-cell__add" type="button" data-modal="modal-agendamento" data-date="' . $day . '" data-time="' . $slotTime . '"><span>Disponível</span><small>' . $slotTime . '</small></button>';
                                }
                                echo '</div>';
                            endforeach;
                        endforeach;
                        ?>
                    </div>
                </div>
            </div><!-- /.agenda-wrap -->
        </div><!-- /#view-agenda -->

        <!-- ─────────────── VIEW: LISTA ─────────────── -->
        <div id="view-lista" hidden>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">
                        <i class="fa-solid fa-calendar-check"></i> Todos os Agendamentos
                    </h2>
                    <div class="table-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input class="table-search" type="text" placeholder="Pesquisar..." data-search="tbl-agendamentos" />
                    </div>
                    <label class="table-date-filter">
                        <span>Data</span>
                        <input type="date" lang="pt-BR" data-filter-date="tbl-agendamentos" aria-label="Filtrar agendamentos por data" />
                    </label>
                </div>
                <div class="table-wrapper">
                    <table class="dash-table" id="tbl-agendamentos">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Observação</th>
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
                                <tr data-date="<?= htmlspecialchars($ag['data']) ?>">
                                    <td><span class="client-name"><?= htmlspecialchars($ag['cliente']) ?></span></td>
                                    <td><?= htmlspecialchars($ag['servico']) ?></td>
                                    <td><?= htmlspecialchars($ag['data_fmt']) ?></td>
                                    <td><?= htmlspecialchars($ag['observacoes'] ?: 'Nenhuma') ?></td>
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
                                                data-servico_id="<?= (int) ($ag['servico_id'] ?? 0) ?>"
                                                data-servicos_ids="<?= htmlspecialchars($ag['servicos_ids'] ?? '') ?>"
                                                data-data="<?= htmlspecialchars($ag['data']) ?>"
                                                data-hora="<?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?>"
                                                data-status="<?= $ag['status'] ?>"
                                                data-observacoes="<?= htmlspecialchars($ag['observacoes'] ?? '') ?>">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button class="btn-action btn-action--whatsapp" title="Lembrete WhatsApp"
                                                data-cliente="<?= htmlspecialchars($ag['cliente']) ?>"
                                                data-telefone="<?= htmlspecialchars($ag['telefone'] ?? '') ?>"
                                                data-servico="<?= htmlspecialchars($ag['servico']) ?>"
                                                data-data="<?= htmlspecialchars($ag['data_fmt']) ?>"
                                                data-hora="<?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?>"
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
                                    <td colspan="7" class="table-empty-cell table-empty-cell--large">Nenhum agendamento encontrado.</td>
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

<?php $pageScripts = ['agendamentos.js'];
include __DIR__ . '/../partials/scripts.php'; ?>

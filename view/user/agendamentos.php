<?php
include_once(__DIR__ . '/../../api/auth/session.php');
include_once(__DIR__ . '/../../config/connection.php');
include_once(__DIR__ . '/../../sql/ServicosSql.php');
include_once(__DIR__ . '/../../sql/AgendamentosSql.php');

$rootPath    = '../../';
$linkBase    = '../';
$activeNav   = 'agendar';
$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Cliente');
$pageTitle   = 'Meus Agendamentos — Tio Preto Barbearia';
$extraCss    = ['assets/css/shared/agenda.css'];
$bodyClass   = 'user-page';

$usuarioId        = (int) $_SESSION['usuario_id'];
$servicos         = ServicosSql::listar();
$pagina           = max(1, (int) ($_GET['pagina'] ?? 1));
$limite           = 8;
$offset           = ($pagina - 1) * $limite;
$total            = AgendamentosSql::contarPorUsuario($usuarioId);
$totalPaginas     = (int) ceil($total / $limite);
$meusAgendamentos = AgendamentosSql::listarPorUsuarioPaginado($usuarioId, $limite, $offset);
$weekStart        = isset($_GET['data']) ? strtotime($_GET['data']) : strtotime('today');
$weekStart        = strtotime('monday this week', $weekStart);
$weekDays         = [];
for ($i = 0; $i < 6; $i++) {
    $weekDays[] = date('Y-m-d', strtotime('+' . $i . ' days', $weekStart));
}
$diasSemana       = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
$meses            = [1 => 'jan', 2 => 'fev', 3 => 'mar', 4 => 'abr', 5 => 'mai', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'set', 10 => 'out', 11 => 'nov', 12 => 'dez'];
$weekLabel        = date('d', $weekStart) . ' a ' . date('d', strtotime('+5 days', $weekStart)) . ' de ' . $meses[(int) date('n', strtotime('+5 days', $weekStart))] . ' de ' . date('Y', strtotime('+5 days', $weekStart));
$agendaData       = AgendamentosSql::listarPorPeriodo(date('Y-m-d', $weekStart), date('Y-m-d', strtotime('+5 days', $weekStart)));
$agendaMap        = [];
foreach ($agendaData as $item) {
    if (($item['status'] ?? '') === 'cancelado') {
        continue;
    }
    $slotKey = substr($item['hora_inicio'], 0, 5);
    $agendaMap[$item['data']][$slotKey][] = $item;

    $inicio = ((int) substr($item['hora_inicio'], 0, 2) * 60) + (int) substr($item['hora_inicio'], 3, 2);
    $fim = ((int) substr($item['hora_fim'], 0, 2) * 60) + (int) substr($item['hora_fim'], 3, 2);
    for ($minuto = $inicio + 30; $minuto < $fim; $minuto += 30) {
        $hora = sprintf('%02d:%02d', intdiv($minuto, 60), $minuto % 60);
        $agendaMap[$item['data']][$hora][] = ['continuacao' => true, 'usuario_id' => (int) $item['usuario_id']];
    }
}
include_once __DIR__ . '/../partials/head_public.php';
?>
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>
<div class='page-banner'>
    <span class='page-banner__eyebrow'>✦ Área do Cliente</span>
    <h1 class='page-banner__title'>Meus <span>Agendamentos</span></h1>
    <p class='page-banner__desc'>Olá, <?= $nomeUsuario ?>! Escolha um dia e agende seu horário.</p>
</div>
<main class="user-agenda">
    <section class="agenda-calendar-section">
        <div class="agenda-calendar-header">
            <h2 class="agenda-calendar-title">
                <i class="fa-regular fa-calendar"></i> Minha Semana
            </h2>
            <div class="agenda-nav agenda-nav--compact">
                <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('-7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                <span class="agenda-nav__label"><?= $weekLabel ?></span>
                <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('+7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
        </div>
        <p class="agenda-mobile-hint"><i class="fa-solid fa-arrows-left-right"></i> Deslize para ver todos os dias</p>
        <div class="agenda-wrap">
            <div class="agenda-scroll">
                <div class="agenda-grid">
                    <div class="agenda-corner"></div>
                    <?php foreach ($weekDays as $index => $day): ?>
                        <div class="agenda-day-head<?= $day === date('Y-m-d') ? ' today' : '' ?>">
                            <?= $diasSemana[$index] ?> <span><?= date('d', strtotime($day)) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php
                    $slots = [];
                    for ($hour = 8; $hour <= 19; $hour++) {
                        foreach ([0, 30] as $minute) {
                            $slots[] = sprintf('%02d:%02d', $hour, $minute);
                        }
                    }
                    foreach ($slots as $slotTime):
                        $isHalf = (int) substr($slotTime, -2) === 30;
                        echo '<div class="agenda-hour' . ($isHalf ? ' agenda-hour--half' : '') . '">' . $slotTime . '</div>';
                        foreach ($weekDays as $day):
                            $appointments = $agendaMap[$day][$slotTime] ?? [];
                            $ownAppointment = null;
                            $continuaProprio = false;
                            foreach ($appointments as $appointment) {
                                if (empty($appointment['continuacao']) && (int) $appointment['usuario_id'] === $usuarioId) {
                                    $ownAppointment = $appointment;
                                    break;
                                }
                                if (!empty($appointment['continuacao']) && (int) ($appointment['usuario_id'] ?? 0) === $usuarioId) {
                                    $continuaProprio = true;
                                }
                            }
                            echo '<div class="agenda-cell' . ($day === date('Y-m-d') ? ' today' : '') . '" data-date="' . $day . '" data-time="' . $slotTime . '">';
                            if ($ownAppointment) {
                                $durationMinutes = (int) ($ownAppointment['duracao_minutos'] ?? 30);
                                $slotsSpan = min(24, max(1, (int) ceil($durationMinutes / 30)));
                                echo '<button class="agenda-appt agenda-appt--meu agenda-appt--slots-' . $slotsSpan . '" type="button" data-own-id="' . (int) $ownAppointment['id'] . '">';
                                echo '<span class="agenda-appt__name">Seu agendamento</span>';
                                echo '<span class="agenda-appt__service">' . htmlspecialchars($ownAppointment['servico']) . ' · ' . $durationMinutes . ' min</span>';
                                echo '</button>';
                            } elseif ($continuaProprio) {
                                echo '<div class="agenda-cell__blocked" aria-hidden="true"></div>';
                            } elseif (!empty($appointments)) {
                                echo '<div class="agenda-appt agenda-appt--ocupado agenda-appt--slots-1"><span class="agenda-appt__name">Ocupado</span><span class="agenda-appt__service">Indisponível</span></div>';
                            } else {
                                echo '<button class="agenda-cell__add" type="button" data-date="' . $day . '" data-time="' . $slotTime . '"><span>Disponível</span><small>' . $slotTime . '</small></button>';
                            }
                            echo '</div>';
                        endforeach;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- MEUS AGENDAMENTOS -->
    <section class="user-agenda-section">
        <div class="user-agenda__actions user-agenda__actions--list">
            <h2 class="user-agenda__heading">
                <i class="fa-solid fa-list user-agenda__heading-icon"></i> Meus Agendamentos
            </h2>
        </div>
        <?php if (empty($meusAgendamentos)): ?>
            <div class="appt-list__empty">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>Você ainda não possui agendamentos.</p>
            </div>
        <?php else: ?>
            <div class="appt-list">
                <?php
                $badgeMap = ['pendente' => 'badge--pending', 'confirmado' => 'badge--confirmed', 'cancelado' => 'badge--cancelled', 'finalizado' => 'badge--finalizado'];
                foreach ($meusAgendamentos as $ag):
                    $bc = $badgeMap[$ag['status']] ?? 'badge--pending';
                    $sl = ucfirst($ag['status']);
                    $ip = !in_array($ag['status'], ['cancelado', 'finalizado'], true);
                ?>
                    <div class="appt-card appt-card--<?= $ag['status'] ?>" data-own-id="<?= (int) $ag['id'] ?>">
                        <div class="appt-card__icon"><i class="fa-solid fa-scissors"></i></div>
                        <div class="appt-card__info">
                            <h3 class="appt-card__servico"><?= htmlspecialchars($ag['servico']) ?></h3>
                            <p class="appt-card__meta">
                                <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($ag['data_fmt']) ?></span>
                                <span><i class="fa-regular fa-clock"></i> <?= htmlspecialchars(substr($ag['hora_inicio'], 0, 5)) ?> – <?= htmlspecialchars(substr($ag['hora_fim'], 0, 5)) ?></span>
                                <span><i class="fa-solid fa-circle-dollar-to-slot"></i> R$ <?= number_format($ag['preco_servico'], 2, ',', '.') ?></span>
                            </p>
                        </div>
                        <div class="appt-card__right">
                            <span class="badge <?= $bc ?>"><?= $sl ?></span>
                            <?php if ($ip): ?>
                                <button class="btn-cancelar" data-cancelar-id="<?= $ag['id'] ?>" data-servico="<?= htmlspecialchars($ag['servico']) ?>">
                                    <i class="fa-solid fa-xmark"></i> Cancelar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($totalPaginas > 1): ?>
                <div class="pagination pagination--spaced">
                    <?php if ($pagina > 1): ?>
                        <a class="pagination-btn" href="?pagina=<?= $pagina - 1 ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-btn--disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    <?php endif; ?>
                    <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                        <a class="pagination-page<?= $p === $pagina ? ' active' : '' ?>" href="?pagina=<?= $p ?>"><?= $p ?></a>
                    <?php endfor; ?>
                    <?php if ($pagina < $totalPaginas): ?>
                        <a class="pagination-btn" href="?pagina=<?= $pagina + 1 ?>"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-btn--disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include __DIR__ . '/../partials/modais/modal-novo-agendamento.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/agendamentos.js" defer></script>
</body>

</html>

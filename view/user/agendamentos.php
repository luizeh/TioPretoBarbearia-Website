<?php
include_once(__DIR__ . '/../../api/auth/session.php');
include_once(__DIR__ . '/../../config/Connection.php');
include_once(__DIR__ . '/../../sql/ServicosSql.php');
include_once(__DIR__ . '/../../sql/AgendamentosSql.php');
include_once(__DIR__ . '/../../sql/HorariosSql.php');

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
for ($i = 0; $i < 7; $i++) {
    $weekDays[] = date('Y-m-d', strtotime('+' . $i . ' days', $weekStart));
}
$diasSemana       = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
$meses            = [1 => 'jan', 2 => 'fev', 3 => 'mar', 4 => 'abr', 5 => 'mai', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'set', 10 => 'out', 11 => 'nov', 12 => 'dez'];
$weekLabel        = date('d', $weekStart) . ' a ' . date('d', strtotime('+6 days', $weekStart)) . ' de ' . $meses[(int) date('n', strtotime('+6 days', $weekStart))] . ' de ' . date('Y', strtotime('+6 days', $weekStart));
$agendaData       = AgendamentosSql::listarPorPeriodo(date('Y-m-d', $weekStart), date('Y-m-d', strtotime('+6 days', $weekStart)));
$horariosMap      = HorariosSql::buscarPorDatas($weekDays);
// O cliente não visualiza dias totalmente fechados — apenas o admin.
// Guarda o índice do dia da semana (0=Seg … 6=Dom) para rotular corretamente.
$visibleDays      = [];
foreach ($weekDays as $idx => $day) {
    $hDiaSemana = $horariosMap[$day] ?? null;
    if ($hDiaSemana && !empty($hDiaSemana['fechado'])) continue;
    $visibleDays[] = ['date' => $day, 'dow' => $idx];
}
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
<main class="user-agenda user-agenda--calendar">
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

    <section class="agenda-calendar-section">
        <div class="agenda-calendar-header">
            <h2 class="agenda-calendar-title">
                <i class="fa-regular fa-calendar"></i> Minha Semana
            </h2>
            <div class="agenda-nav agenda-nav--compact">
                <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('-7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                <span class="agenda-nav__label"><?= $weekLabel ?></span>
                <div class="agenda-goto">
                    <i class="fa-regular fa-calendar-days"></i>
                    <input type="date" class="agenda-goto-input" lang="pt-BR" value="<?= date('Y-m-d', $weekStart) ?>" title="Escolha uma data para ir direto à semana dela" aria-label="Ir para uma data">
                    <a class="agenda-goto-hoje" href="?data=<?= date('Y-m-d') ?>">Hoje</a>
                </div>
                <a class="agenda-nav__btn" href="?data=<?= date('Y-m-d', strtotime('+7 days', $weekStart)) ?>"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
        </div>
        <p class="agenda-mobile-hint"><i class="fa-solid fa-arrows-left-right"></i> Deslize para ver todos os dias</p>
        <div class="agenda-wrap">
            <div class="agenda-scroll">
                <div class="agenda-grid agenda-grid--cliente" style="--agenda-dias: <?= max(1, count($visibleDays)) ?>">
                    <div class="agenda-corner"></div>
                    <?php foreach ($visibleDays as $entry): $day = $entry['date']; ?>
                        <div class="agenda-day-head<?= $day === date('Y-m-d') ? ' today' : '' ?>">
                            <?= $diasSemana[$entry['dow']] ?> <span><?= date('d', strtotime($day)) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php
                    $slots = [];
                    for ($hour = 8; $hour <= 19; $hour++) {
                        foreach ([0, 30] as $minute) {
                            $slots[] = sprintf('%02d:%02d', $hour, $minute);
                        }
                    }
                    // ── Overlay de fora-de-expediente (caixa única) ──
                    // O cliente não vê dias fechados (foram removidos das colunas). Aqui só
                    // tratamos, nos dias abertos, os trechos antes da abertura e depois do
                    // fechamento — que recebem a mesma caixa "Fechado". O miolo fica normal.
                    // $_col usa a posição VISUAL entre os dias visíveis (não o dia da semana).
                    $totalSlots = count($slots);
                    foreach ($visibleDays as $_pos => $entry):
                        $_hD  = $horariosMap[$entry['date']] ?? null;
                        $_col = $_pos + 2;
                        $_abertura = $_hD ? substr($_hD['abertura'],   0, 5) : '08:00';
                        $_fecho    = $_hD ? substr($_hD['fechamento'], 0, 5) : '20:00';
                        // Trecho antes da abertura
                        $_antes = 0;
                        foreach ($slots as $_s) {
                            if ($_s < $_abertura) $_antes++;
                            else break;
                        }
                        if ($_antes > 0) {
                            $_lbl = $_antes >= 3 ? '<span>Fechado</span>' : '';
                            echo '<div class="agenda-cell--dia-fechado-col" style="grid-column:' . $_col . ';grid-row:2/span ' . $_antes . '">' . $_lbl . '</div>';
                        }
                        // Trecho depois do fechamento
                        $_inicioDepois = null;
                        foreach ($slots as $_i => $_s) {
                            if ($_s >= $_fecho) { $_inicioDepois = $_i; break; }
                        }
                        if ($_inicioDepois !== null) {
                            $_depois = $totalSlots - $_inicioDepois;
                            $_lbl = $_depois >= 3 ? '<span>Fechado</span>' : '';
                            echo '<div class="agenda-cell--dia-fechado-col" style="grid-column:' . $_col . ';grid-row:' . ($_inicioDepois + 2) . '/span ' . $_depois . '">' . $_lbl . '</div>';
                        }
                    endforeach;
                    $hojeStr      = date('Y-m-d'); // referência de "hoje" (mesmo fuso do restante da agenda)
                    $horaAgoraStr = date('H:i');   // horário atual — slots de hoje anteriores a isto já passaram
                    foreach ($slots as $slotTime):
                        $isHalf = (int) substr($slotTime, -2) === 30;
                        echo '<div class="agenda-hour' . ($isHalf ? ' agenda-hour--half' : '') . '">' . $slotTime . '</div>';
                        foreach ($visibleDays as $entry):
                            $day         = $entry['date'];
                            $hDia        = $horariosMap[$day] ?? null;
                            $abertura    = $hDia ? substr($hDia['abertura'],   0, 5) : '08:00';
                            $fecho       = $hDia ? substr($hDia['fechamento'], 0, 5) : '20:00';
                            if ($slotTime < $abertura || $slotTime >= $fecho) continue; // overlay fora-de-expediente cobre a célula
                            $emBloqueio  = false;
                            $bloqueioDesc = '';
                            foreach (($hDia['bloqueios'] ?? []) as $blq) {
                                if ($slotTime >= substr($blq['hora_inicio'], 0, 5) && $slotTime < substr($blq['hora_fim'], 0, 5)) {
                                    $emBloqueio  = true;
                                    $bloqueioDesc = $blq['descricao'] ?? '';
                                    break;
                                }
                            }
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
                            if ($emBloqueio) {
                                $label = htmlspecialchars($bloqueioDesc ?: 'Indisponível');
                                echo '<div class="agenda-cell__fechado agenda-cell__fechado--bloqueio" aria-hidden="true">' . $label . '</div>';
                            } elseif ($ownAppointment) {
                                $durationMinutes = (int) ($ownAppointment['duracao_minutos'] ?? 30);
                                $slotsSpan = min(24, max(1, (int) ceil($durationMinutes / 30)));
                                echo '<button class="agenda-appt agenda-appt--meu agenda-appt--slots-' . $slotsSpan . '" type="button" data-own-id="' . (int) $ownAppointment['id'] . '">';
                                echo '<span class="agenda-appt__name">Seu agendamento</span>';
                                echo '<span class="agenda-appt__service">' . htmlspecialchars($ownAppointment['servico']) . ' · ' . $durationMinutes . ' min</span>';
                                echo '</button>';
                            } elseif ($continuaProprio) {
                                echo '<div class="agenda-cell__blocked" aria-hidden="true"></div>';
                            } elseif (!empty($appointments)) {
                                echo '<div class="agenda-appt agenda-appt--ocupado agenda-appt--slots-1"><span class="agenda-appt__name">Indisponível</span></div>';
                            } elseif ($day === $hojeStr && $slotTime < $horaAgoraStr) {
                                echo '<div class="agenda-appt agenda-appt--ocupado agenda-appt--slots-1"><span class="agenda-appt__name">Indisponível</span></div>';
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
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include __DIR__ . '/../partials/modais/modal-novo-agendamento.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/agendamentos.js" defer></script>
</body>

</html>
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
$extraCss    = ['assets/css/agenda.css'];
$bodyClass   = 'user-page';

$usuarioId        = (int) $_SESSION['usuario_id'];
$servicos         = ServicosSql::listar();
$pagina           = max(1, (int) ($_GET['pagina'] ?? 1));
$limite           = 8;
$offset           = ($pagina - 1) * $limite;
$total            = AgendamentosSql::contarPorUsuario($usuarioId);
$totalPaginas     = (int) ceil($total / $limite);
$meusAgendamentos = AgendamentosSql::listarPorUsuarioPaginado($usuarioId, $limite, $offset);
include_once __DIR__ . '/../partials/head_public.php';
?>
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>
<div class='page-banner'>
    <span class='page-banner__eyebrow'>✦ Área do Cliente</span>
    <h1 class='page-banner__title'>Meus <span>Agendamentos</span></h1>
    <p class='page-banner__desc'>Olá, <?= $nomeUsuario ?>! Escolha um dia e agende seu horário.</p>
</div>
<div class='user-agenda'>
    <!-- CALENDARIO + SLOTS -->
    <section class="agenda-calendar-section">
        <div class="agenda-calendar-header">
            <h2 class="agenda-calendar-title">
                <i class="fa-regular fa-calendar"></i> Selecione um Dia
            </h2>
            <input type="date" id="agenda-date-picker" class="modal-input"
                style="max-width:200px;"
                min="2026-07-09"
                value="2026-07-09" />
        </div>
        <div id="agenda-slots-container" class="agenda-slots-grid">
            <p class="agenda-slots-loading"><i class="fa-solid fa-spinner fa-spin"></i> Carregando...</p>
        </div>
    </section>

    <!-- MEUS AGENDAMENTOS -->
    <section style="margin-top:40px;">
        <div class="user-agenda__actions" style="margin-bottom:20px;justify-content:space-between;align-items:center;">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;">
                <i class="fa-solid fa-list" style="color:var(--gold);"></i> Meus Agendamentos
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
                    $ip = $ag['status'] === 'pendente';
                ?>
                    <div class="appt-card appt-card--<?= $ag['status'] ?>">
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
                <div class="pagination" style="margin-top:28px;">
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
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include __DIR__ . '/../partials/modais/modal-novo-agendamento.php'; ?>
<?php include __DIR__ . '/../partials/modais/modal-cancelar-agendamento.php'; ?>
<script src="<?= $rootPath ?>node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?= $rootPath ?>assets/js/swal-theme.js"></script>
<script src="<?= $rootPath ?>assets/js/public.js"></script>
<script src="<?= $rootPath ?>assets/js/cart.js"></script>
<script src="<?= $rootPath ?>assets/js/user-agendamentos.js"></script>
</body>

</html>
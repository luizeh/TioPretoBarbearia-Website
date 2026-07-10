<?php
include_once __DIR__ . '/../../api/auth/session.php';
require_once __DIR__ . '/../../sql/NotificacoesSql.php';
$rootPath = '../../';
$linkBase = '../';
$activeNav = 'notificacoes';
$pageTitle = 'Notificações';
$bodyClass = 'user-page';
$usuarioId = (int) $_SESSION['usuario_id'];
$notificacoes = NotificacoesSql::listarPorUsuario($usuarioId);
?>
<?php include_once __DIR__ . '/../partials/head_public.php'; ?>
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>
<div class="page-banner">
    <span class="page-banner__eyebrow">Área do Cliente</span>
    <h1 class="page-banner__title">Notificações</h1>
    <p class="page-banner__desc">Acompanhe as atualizações dos seus agendamentos.</p>
</div>
<main class="user-agenda notifications-page">
    <div class="notifications-toolbar">
        <h2><i class="fa-solid fa-bell"></i> Atualizações</h2>
        <?php if ($notificacoes): ?><button type="button" class="btn-modal-secondary" id="marcar-todas-notificacoes">Marcar todas como lidas</button><?php endif; ?>
    </div>
    <div class="notifications-list">
        <?php foreach ($notificacoes as $notificacao): ?>
            <article class="notification-item<?= !$notificacao['lida'] ? ' notification-item--unread' : '' ?>">
                <div class="notification-item__icon"><i class="fa-solid fa-bell"></i></div>
                <div class="notification-item__content">
                    <h3><?= htmlspecialchars($notificacao['titulo']) ?></h3>
                    <p><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                    <small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($notificacao['created_at']))) ?></small>
                </div>
                <?php if (!$notificacao['lida']): ?><button type="button" class="notification-item__read" data-mark-notification="<?= (int) $notificacao['id'] ?>">Marcar como lida</button><?php endif; ?>
            </article>
        <?php endforeach; ?>
        <?php if (!$notificacoes): ?><div class="appt-list__empty"><i class="fa-regular fa-bell-slash"></i><p>Nenhuma notificação.</p></div><?php endif; ?>
    </div>
</main>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/notificacoes.js" defer></script>
</body>
</html>

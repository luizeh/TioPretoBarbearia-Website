<?php
include_once __DIR__ . '/../../api/auth/session.php';
require_once __DIR__ . '/../../sql/PedidosSql.php';

$rootPath = '../../';
$linkBase = '../';
$activeNav = '';
$pageTitle = 'Meus Pedidos';
$bodyClass = 'user-page pedidos-page';
$pedidos = PedidosSql::listarPorUsuario((int) $_SESSION['usuario_id']);
$statusClasses = [
    'recebido' => 'badge--recebido',
    'preparando' => 'badge--preparando',
    'pronto' => 'badge--pronto',
    'entregue' => 'badge--entregue',
    'cancelado' => 'badge--cancelled',
];

include_once __DIR__ . '/../partials/head_public.php';
include_once __DIR__ . '/../partials/header_public.php';
?>

<div class="page-banner">
    <span class="page-banner__eyebrow">Área do Cliente</span>
    <h1 class="page-banner__title">Meus Pedidos</h1>
    <p class="page-banner__desc">Acompanhe suas compras e o status de cada pedido.</p>
</div>

<main class="user-agenda">
    <?php foreach ($pedidos as $pedido): ?>
        <?php $status = (string) $pedido['status']; ?>
        <article class="appt-card appt-card--pedido">
            <div class="appt-card__icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="appt-card__info">
                <h2 class="appt-card__servico">Pedido #<?= (int) $pedido['id'] ?></h2>
                <p class="appt-card__meta">
                    <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($pedido['data_fmt']) ?></span>
                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($pedido['endereco']) ?></span>
                </p>
            </div>
            <div class="appt-card__right">
                <strong>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
                <span class="badge <?= $statusClasses[$status] ?? 'badge--pending' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (!$pedidos): ?>
        <div class="appt-list__empty">
            <i class="fa-solid fa-receipt"></i>
            <p>Você ainda não realizou pedidos.</p>
        </div>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

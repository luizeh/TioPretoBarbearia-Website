<?php

/**
 * Partial: header_public.php
 * Cabeçalho público reutilizável para páginas de cliente.
 *
 * Variáveis esperadas antes do include:
 *   $rootPath  — caminho até a raiz do projeto (ex: '../' de view/, '../../' de view/user/)
 *   $linkBase  — caminho até view/ (ex: '' de view/, '../' de view/user/)
 *   $activeNav — chave da nav ativa: 'inicio' | 'produtos' | 'servicos' | 'agendar'
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rootPath  = $rootPath  ?? '../';
$linkBase  = $linkBase  ?? '';
$activeNav = $activeNav ?? '';
require_once __DIR__ . '/../../sql/NotificacoesSql.php';
$notificacoesHeader = !empty($_SESSION['usuario_id']) && empty($_SESSION['usuario_admin']) ? NotificacoesSql::listarPorUsuario((int) $_SESSION['usuario_id'], 5) : [];
$notificacoesNaoLidas = count(array_filter($notificacoesHeader, static fn(array $item): bool => !(bool) $item['lida']));
?>
<header>
    <div class="header-logo">
        <img src="<?= $rootPath ?>assets/img/tiopretonb.png" alt="Tio Preto Barbearia" />
        <div class="brand-text">
            <span class="brand-name">Tio Preto</span>
            <span class="brand-sub">Barbearia</span>
        </div>
    </div>

    <nav>
        <a href="<?= $linkBase ?>index.php" <?= $activeNav === 'inicio'    ? ' class="nav-active"' : '' ?>>Início</a>
        <a href="<?= $linkBase ?>catalogo.php" <?= $activeNav === 'produtos'  ? ' class="nav-active"' : '' ?>>Produtos</a>
        <a href="<?= $linkBase ?>user/servicos.php" <?= $activeNav === 'servicos' ? ' class="nav-active"' : '' ?>>Serviços</a>
        <a href="<?= $linkBase ?>user/agendamentos.php" <?= $activeNav === 'agendar'  ? ' class="nav-active"' : '' ?>>Agendar Horário</a>
        <?php if (!empty($_SESSION['usuario_id']) && empty($_SESSION['usuario_admin'])): ?>
            <a href="<?= $linkBase ?>user/notificacoes.php" <?= $activeNav === 'notificacoes' ? ' class="nav-active"' : '' ?>>Notificações</a>
        <?php endif; ?>
    </nav>

    <div class="header-actions">
        <?php if (!empty($_SESSION['usuario_admin'])): ?>
            <a href="<?= $linkBase ?>admin/dashboard.php" class="btn-back-dashboard">
                <i class="fa-solid fa-gauge-high"></i> Painel Admin
            </a>
        <?php endif; ?>

        <!-- Carrinho -->
        <div class="cart-menu" id="headerCart">
            <button class="cart-menu__trigger" type="button" aria-label="Carrinho">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-badge" id="cartBadge">0</span>
            </button>
            <div class="cart-menu__dropdown">
                <div class="cart-menu__header">
                    <span class="cart-menu__title">Carrinho</span>
                    <button class="cart-menu__clear" id="cartClear" type="button">Limpar tudo</button>
                </div>
                <div class="cart-menu__empty" id="cartEmpty">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <p>Carrinho vazio</p>
                </div>
                <div class="cart-menu__items" id="cartItems"></div>
                <div class="cart-menu__footer" id="cartFooter">
                    <a href="<?= $linkBase ?>user/carrinho.php" class="btn-cart-checkout">Finalizar Pedido</a>
                    <a href="<?= $linkBase ?>user/carrinho.php" class="cart-menu__full-link">Ver carrinho completo</a>
                </div>
            </div>
        </div>

        <!-- Usuário -->
    <?php if (!empty($_SESSION['usuario_id']) && empty($_SESSION['usuario_admin'])): ?>
        <div class="notifications-menu" id="headerNotifications">
            <button class="notifications-menu__trigger" type="button" aria-label="Notificações">
                <i class="fa-solid fa-bell"></i>
                <?php if ($notificacoesNaoLidas): ?><span class="notifications-menu__badge"><?= $notificacoesNaoLidas ?></span><?php endif; ?>
            </button>
            <div class="notifications-menu__dropdown">
                <div class="notifications-menu__header"><strong>Notificações</strong><button type="button" id="headerNotificationsReadAll">Marcar como lidas</button></div>
                <div class="notifications-menu__items">
                    <?php if (!$notificacoesHeader): ?>
                        <p class="notifications-menu__empty">Nenhuma notificação.</p>
                    <?php else: foreach ($notificacoesHeader as $notificacao): ?>
                        <button type="button" class="notifications-menu__item<?= !$notificacao['lida'] ? ' is-unread' : '' ?>" data-header-notification="<?= (int) $notificacao['id'] ?>">
                            <strong><?= htmlspecialchars($notificacao['titulo']) ?></strong>
                            <span><?= htmlspecialchars($notificacao['mensagem']) ?></span>
                            <small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($notificacao['created_at']))) ?></small>
                        </button>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="header-user">
            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <div class="user-menu" id="headerUserMenu">
                    <button class="user-menu__trigger" type="button">
                        <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'V', 0, 1)) ?></div>
                        <span class="user-menu__name"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Visitante') ?></span>
                        <i class="fa-solid fa-chevron-down user-menu__chevron"></i>
                    </button>
                    <div class="user-menu__dropdown">
                        <a href="<?= $linkBase ?>user/perfil.php" class="user-menu__item">
                            <i class="fa-solid fa-user"></i> Ver Perfil
                        </a>
                        <a href="<?= $linkBase ?>user/carrinho.php" class="user-menu__item">
                            <i class="fa-solid fa-bag-shopping"></i> Meu Carrinho
                        </a>
                        <a href="<?= $linkBase ?>user/pedidos.php" class="user-menu__item">
                            <i class="fa-solid fa-receipt"></i> Meus Pedidos
                        </a>
                        <a href="<?= $rootPath ?>api/auth/logout.php" class="user-menu__item user-menu__item--danger">
                            <i class="fa-solid fa-right-from-bracket"></i> Sair
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= $linkBase ?>login.php" class="btn-header-login">Entrar</a>
            <?php endif; ?>
        </div>

        <button class="header-burger" id="mobileMenuToggle" type="button" aria-label="Abrir menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>
<script src="<?= $rootPath ?>assets/js/public/cart-menu.js" defer></script>
<script src="<?= $rootPath ?>assets/js/public/header.js" defer></script>
<script src="<?= $rootPath ?>assets/js/public/modal-handler.js" defer></script>

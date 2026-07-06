<!-- ══════════════ TOPBAR ══════════════ -->
<header class="topbar">
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="topbar-greeting">
        <span class="topbar-date" id="topbarDate"></span>
    </div>
    <div class="topbar-user">
        <span class="topbar-user-name"><?= htmlspecialchars($usuario['nome'] ?? 'Visitante') ?></span>
        <div class="topbar-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>
</header>
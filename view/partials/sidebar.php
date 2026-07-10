<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="../../assets/img/tiopretonb.png" alt="Tio Preto Barbearia" />
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>
        <a href="clientes.php" class="nav-item <?= ($activePage ?? '') === 'clientes' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span>Clientes</span>
        </a>
        <a href="agendamentos.php" class="nav-item <?= ($activePage ?? '') === 'agendamentos' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Agendamentos</span>
        </a>
        <a href="servicos.php" class="nav-item <?= ($activePage ?? '') === 'servicos' ? 'active' : '' ?>">
            <i class="fa-solid fa-scissors"></i>
            <span>Serviços</span>
        </a>
        <a href="produtos.php" class="nav-item <?= ($activePage ?? '') === 'produtos' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-open"></i>
            <span>Produtos</span>
        </a>
        <a href="site-editor.php" class="nav-item <?= ($activePage ?? '') === 'site-editor' ? 'active' : '' ?>">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Editor do Site</span>
        </a>
        <a href="horarios.php" class="nav-item <?= ($activePage ?? '') === 'horarios' ? 'active' : '' ?>">
            <i class="fa-regular fa-clock"></i>
            <span>Horários</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../../view/catalogo.php" target="_blank" class="nav-item nav-item--site">
            <i class="fa-solid fa-globe"></i>
            <span>Ver Site</span>
        </a>
        <a href="../../api/auth/logout.php" class="nav-item nav-item--logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>
<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="../assets/img/tiopretonb.png" alt="Tio Preto Barbearia" />
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
        <a href="#" class="nav-item <?= ($activePage ?? '') === 'agendamentos' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Agendamentos</span>
        </a>
        <a href="#" class="nav-item <?= ($activePage ?? '') === 'servicos' ? 'active' : '' ?>">
            <i class="fa-solid fa-scissors"></i>
            <span>Serviços</span>
        </a>
        <a href="#" class="nav-item <?= ($activePage ?? '') === 'produtos' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-open"></i>
            <span>Produtos</span>
        </a>
        <a href="#" class="nav-item <?= ($activePage ?? '') === 'relatorios' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Relatórios</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="#" class="nav-item nav-item--settings">
            <i class="fa-solid fa-gear"></i>
            <span>Configurações</span>
        </a>
        <a href="../api/auth/logout.php" class="nav-item nav-item--logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>
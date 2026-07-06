<?php
$activePage = 'clientes';
include_once(__DIR__ . '/../controllers/clientes.controller.php');
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../img/favicon.png" type="image/png" />
    <title>Clientes — Tio Preto Barbearia</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>

<body>

    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- ══════════════ CONTEÚDO PRINCIPAL ══════════════ -->
    <div class="main-wrapper">

        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <!-- PAGE CONTENT -->
        <main class="page-content">

            <!-- Título da página -->
            <div class="page-header">
                <div>
                    <p class="page-eyebrow">✦ Gerenciamento</p>
                    <h1 class="page-title page-title--lg">Clientes</h1>
                </div>
            </div>

            <!-- Banner total de clientes -->
            <div class="clientes-stat-banner">
                <div class="clientes-stat-banner__icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="clientes-stat-banner__info">
                    <span class="clientes-stat-banner__count"><?= count($usuarios) ?></span>
                    <span class="clientes-stat-banner__label">clientes cadastrados</span>
                </div>
            </div>

            <!-- ── TABELA DE CLIENTES ── -->
            <div class="dashboard-card clientes-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">
                        <i class="fa-solid fa-users"></i> Todos os Clientes
                    </h2>
                    <div class="table-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input class="table-search" type="text" placeholder="Pesquisar clientes..." data-search="tbl-clientes" />
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="dash-table" id="tbl-clientes">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Cidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><span class="client-name"><?= htmlspecialchars($u['nome']) . ' ' . htmlspecialchars($u['sobrenome']) ?></span></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['telefone']) ?></td>
                                    <td><?= htmlspecialchars($u['cidade']) ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn-action btn-action--view" title="Ver">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-action--edit" title="Editar">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button class="btn-action btn-action--delete" title="Excluir">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                        </tbody>
                    </table>
                </div>

                <!-- Paginação estática -->
                <div class="pagination">
                    <button class="pagination-btn" disabled>
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="pagination-page active">1</button>
                    <button class="pagination-page">2</button>
                    <button class="pagination-page">3</button>
                    <button class="pagination-btn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>
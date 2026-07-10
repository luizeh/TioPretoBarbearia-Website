<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';
require_once __DIR__ . '/../../sql/SiteConfigSql.php';

$activePage = 'site-editor';
$pageTitle  = 'Editor do Site';
$config     = SiteConfigSql::buscarTodos();

include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-wrapper">

    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <main class="page-content">

        <!-- Cabeçalho -->
        <div class="page-header">
            <div>
                <p class="page-eyebrow">✦ Conteúdo</p>
                <h1 class="page-title page-title--lg">Editor do Site</h1>
            </div>
        </div>

        <!-- ── Landing Page ── -->
        <div class="dashboard-card site-editor-card" id="grupo-landing">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-house"></i> Página Inicial (Landing)
                </h2>
            </div>

            <div class="site-editor-fields">
                <?php foreach ($config['landing'] ?? [] as $item): ?>
                    <div class="site-editor-field">
                        <label class="site-editor-label" for="field-<?= htmlspecialchars($item['chave']) ?>">
                            <?= htmlspecialchars($item['titulo']) ?>
                        </label>
                        <textarea
                            id="field-<?= htmlspecialchars($item['chave']) ?>"
                            class="site-editor-textarea"
                            data-chave="<?= htmlspecialchars($item['chave']) ?>"
                            data-grupo="landing"
                            rows="2"><?= htmlspecialchars($item['valor']) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="site-editor-actions">
                <button class="btn-primary" data-save-grupo="landing">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar Landing Page
                </button>
                <a href="../../view/index.php" target="_blank" class="btn-secondary">
                    <i class="fa-solid fa-eye"></i> Ver página
                </a>
            </div>
        </div>

        <!-- ── Rodapé ── -->
        <div class="dashboard-card site-editor-card" id="grupo-footer">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-shoe-prints fa-rotate-270"></i> Rodapé (Footer)
                </h2>
            </div>

            <div class="site-editor-fields">
                <?php foreach ($config['footer'] ?? [] as $item): ?>
                    <div class="site-editor-field">
                        <label class="site-editor-label" for="field-<?= htmlspecialchars($item['chave']) ?>">
                            <?= htmlspecialchars($item['titulo']) ?>
                        </label>
                        <textarea
                            id="field-<?= htmlspecialchars($item['chave']) ?>"
                            class="site-editor-textarea"
                            data-chave="<?= htmlspecialchars($item['chave']) ?>"
                            data-grupo="footer"
                            rows="2"><?= htmlspecialchars($item['valor']) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="site-editor-actions">
                <button class="btn-primary" data-save-grupo="footer">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar Rodapé
                </button>
                <a href="../../view/catalogo.php" target="_blank" class="btn-secondary">
                    <i class="fa-solid fa-eye"></i> Ver site
                </a>
            </div>
        </div>

    </main>
</div>

<?php $pageScripts = ['site-editor.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
<?php

/**
 * Partial: Paginação server-side
 *
 * Variáveis esperadas (já disponíveis pelo controller):
 *   int $pagina       — página atual
 *   int $totalPaginas — total de páginas
 */
$pagina       = isset($pagina)       ? (int) $pagina       : 1;
$totalPaginas = isset($totalPaginas) ? (int) $totalPaginas : 1;

if ($totalPaginas <= 1) return;

$_base = htmlspecialchars($_SERVER['PHP_SELF']);
?>
<div class="pagination">
    <?php if ($pagina > 1): ?>
        <a class="pagination-btn" href="<?= $_base ?>?pagina=<?= $pagina - 1 ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
    <?php else: ?>
        <span class="pagination-btn pagination-btn--disabled">
            <i class="fa-solid fa-chevron-left"></i>
        </span>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
        <a class="pagination-page<?= $p === $pagina ? ' active' : '' ?>"
            href="<?= $_base ?>?pagina=<?= $p ?>">
            <?= $p ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a class="pagination-btn" href="<?= $_base ?>?pagina=<?= $pagina + 1 ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    <?php else: ?>
        <span class="pagination-btn pagination-btn--disabled">
            <i class="fa-solid fa-chevron-right"></i>
        </span>
    <?php endif; ?>
</div>
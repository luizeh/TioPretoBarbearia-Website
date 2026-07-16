<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/DashboardSql.php');
include_once(__DIR__ . '/../sql/LogsSql.php');

// Paginação dos logs do sistema (server-side).
$logsPorPagina = 12;
$paginaLogs    = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($paginaLogs < 1) {
    $paginaLogs = 1;
}

$totalLogs        = LogsSql::contar();
$totalPaginasLogs = max(1, (int) ceil($totalLogs / $logsPorPagina));
if ($paginaLogs > $totalPaginasLogs) {
    $paginaLogs = $totalPaginasLogs;
}

$offsetLogs = ($paginaLogs - 1) * $logsPorPagina;
$logs       = LogsSql::listar($logsPorPagina, $offsetLogs);

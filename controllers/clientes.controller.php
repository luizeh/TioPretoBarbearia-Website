<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/DashboardSql.php');

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$limite = 10;

$offset = ($pagina - 1) * $limite;

$usuario  = DashboardSql::buscarUsuario($_SESSION['usuario_id']);

$totalUsuarios = DashboardSql::contarUsuarios();
$totalPaginas = ceil($totalUsuarios / $limite);

$usuarios = DashboardSql::listarUsuarios($limite, $offset);

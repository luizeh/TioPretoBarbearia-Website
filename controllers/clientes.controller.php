<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../config/connection.php');
include_once(__DIR__ . '/../sql/dashboard.sql.php');

$pdo = Connection::getConnection();

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$limite = 10;

$offset = ($pagina - 1) * $limite;

$usuario  = buscarUsuario($pdo, $_SESSION['usuario_id']);

$totalUsuarios = contarUsuarios($pdo);
$totalPaginas = ceil($totalUsuarios / $limite);

$usuarios = listarUsuarios($pdo, $limite, $offset);
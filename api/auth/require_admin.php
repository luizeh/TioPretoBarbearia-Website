<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../../view/login.php');
    exit;
}

if (empty($_SESSION['usuario_admin'])) {
    header('Location: ../../view/user/agendamentos.php');
    exit;
}

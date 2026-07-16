<?php

require_once __DIR__ . '/../../helpers/helpers.php';
helpers::iniciarSessao();

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../../view/login.php');
    exit;
}

if (empty($_SESSION['usuario_admin'])) {
    header('Location: ../../view/user/agendamentos.php');
    exit;
}

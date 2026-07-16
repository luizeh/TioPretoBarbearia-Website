<?php

require_once __DIR__ . '/../../helpers/helpers.php';
helpers::iniciarSessao();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

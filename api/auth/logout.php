<?php

require_once __DIR__ . '/../../helpers/helpers.php';
helpers::iniciarSessao();
session_destroy();

header('Location: ../../view/login.php');
exit;

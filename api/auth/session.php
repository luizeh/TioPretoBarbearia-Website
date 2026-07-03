<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit;
}

<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/ServicosSql.php');

$servicos      = ServicosSql::listar();
$totalServicos = count($servicos);

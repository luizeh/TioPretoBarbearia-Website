<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/DashboardSql.php');
include_once(__DIR__ . '/../sql/AgendamentosSql.php');
include_once(__DIR__ . '/../sql/ServicosSql.php');

$proximosAgendamentos = AgendamentosSql::proximosAgendamentos(8);
$servicos             = ServicosSql::listar();

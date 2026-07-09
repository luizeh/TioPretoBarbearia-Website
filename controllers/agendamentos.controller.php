<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/AgendamentosSql.php');
include_once(__DIR__ . '/../sql/ServicosSql.php');

$agendamentos     = AgendamentosSql::listarTodos(200, 0);
$totalAgendamentos = count($agendamentos);
$stats            = AgendamentosSql::estatisticas();
$servicos         = ServicosSql::listar(); // para o select do modal

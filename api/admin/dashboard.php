<?php

/**
 * api/admin/dashboard.php
 * Retorna estatísticas para o painel do dashboard.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/Connection.php';
require_once __DIR__ . '/../../sql/DashboardSql.php';
require_once __DIR__ . '/../../sql/AgendamentosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$stats   = DashboardSql::estatisticas();
$proximos = AgendamentosSql::proximosAgendamentos(10);

helpers::resposta_json(true, 'OK', [
    'stats'    => $stats,
    'proximos' => $proximos,
], 200);

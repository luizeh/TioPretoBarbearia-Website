<?php

include_once(__DIR__ . '/../api/auth/session.php');
include_once(__DIR__ . '/../sql/AgendamentosSql.php');
include_once(__DIR__ . '/../sql/ServicosSql.php');
include_once(__DIR__ . '/../sql/ClientesSql.php');

$agendamentos     = AgendamentosSql::listarTodos(200, 0);
$totalAgendamentos = count($agendamentos);
$stats            = AgendamentosSql::estatisticas();
$servicos         = ServicosSql::listar();
$clientes         = ClientesSql::listar(200, 0);

$rangeStart = $_GET['data'] ?? date('Y-m-d');
$rangeStart = strtotime($rangeStart);
$weekStart  = strtotime('monday this week', $rangeStart);
$weekEnd    = strtotime('+6 days', $weekStart);
$weekDates  = [];
for ($i = 0; $i < 7; $i++) {
    $weekDates[] = date('Y-m-d', strtotime('+' . $i . ' days', $weekStart));
}
$agendaData = AgendamentosSql::listarPorPeriodo(date('Y-m-d', $weekStart), date('Y-m-d', $weekEnd));
$agendaMap  = [];
foreach ($agendaData as $item) {
    $slotKey = substr($item['hora_inicio'], 0, 5);
    $agendaMap[$item['data']][$slotKey][] = $item;

    $inicio = ((int) substr($item['hora_inicio'], 0, 2) * 60) + (int) substr($item['hora_inicio'], 3, 2);
    $fim = ((int) substr($item['hora_fim'], 0, 2) * 60) + (int) substr($item['hora_fim'], 3, 2);
    for ($minuto = $inicio + 30; $minuto < $fim; $minuto += 30) {
        $hora = sprintf('%02d:%02d', intdiv($minuto, 60), $minuto % 60);
        $agendaMap[$item['data']][$hora][] = ['continuacao' => true];
    }
}

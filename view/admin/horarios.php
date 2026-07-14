<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';
require_once __DIR__ . '/../../sql/HorariosSql.php';

$activePage = 'horarios';
$pageTitle  = 'Horários de Funcionamento';
$horarios   = HorariosSql::buscarTodos();
$nomes      = HorariosSql::nomesDias();
$bloqueios  = HorariosSql::buscarTodosBloqueios();
$periodos   = HorariosSql::buscarTodosPeriodos();
$abrevDias  = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb', 7 => 'Dom'];

include __DIR__ . '/../partials/head.php';
?>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-wrapper">

    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <main class="page-content">

        <!-- Cabeçalho -->
        <div class="page-header">
            <div>
                <p class="page-eyebrow">✦ Configurações</p>
                <h1 class="page-title page-title--lg">Horários de Funcionamento</h1>
            </div>
        </div>

        <!-- Card principal -->
        <div class="dashboard-card horarios-card">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-regular fa-clock"></i> Horários por dia da semana
                </h2>
                <button type="button" class="btn-primary btn-primary--compact" id="btn-horarios-salvar-todos">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar Todos
                </button>
            </div>

            <div class="horarios-table-wrap">
                <table class="dash-table horarios-table" id="tbl-horarios">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>Abertura</th>
                            <th>Fechamento</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($dia = 1; $dia <= 7; $dia++):
                            $h = $horarios[$dia] ?? null;
                            $fechado  = $h ? (bool) $h['fechado'] : true;
                            $abertura = $h ? substr($h['abertura'],   0, 5) : '08:00';
                            $fecho    = $h ? substr($h['fechamento'], 0, 5) : '20:00';
                        ?>
                            <tr data-dia="<?= $dia ?>" class="horario-row<?= $fechado ? ' horario-row--fechado' : '' ?>">
                                <td class="horario-dia-nome"><?= htmlspecialchars($nomes[$dia]) ?></td>
                                <td data-label="Abertura">
                                    <input
                                        type="time"
                                        class="horario-input horario-abertura"
                                        value="<?= htmlspecialchars($abertura) ?>"
                                        <?= $fechado ? 'disabled' : '' ?>
                                        step="1800" />
                                </td>
                                <td data-label="Fechamento">
                                    <input
                                        type="time"
                                        class="horario-input horario-fechamento"
                                        value="<?= htmlspecialchars($fecho) ?>"
                                        <?= $fechado ? 'disabled' : '' ?>
                                        step="1800" />
                                </td>
                                <td data-label="Status">
                                    <label class="horario-toggle">
                                        <input
                                            type="checkbox"
                                            class="horario-fechado-check"
                                            <?= $fechado ? 'checked' : '' ?> />
                                        <span class="horario-toggle__track"></span>
                                        <span class="horario-toggle__label">
                                            <?= $fechado ? 'Fechado' : 'Aberto' ?>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn-primary btn-primary--compact horario-salvar"
                                        data-dia="<?= $dia ?>">
                                        <i class="fa-solid fa-floppy-disk"></i> Salvar
                                    </button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Bloqueios recorrentes ── -->
        <div class="dashboard-card horarios-card" id="card-bloqueios">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-ban"></i> Bloqueios de Horário
                </h2>
            </div>

            <div class="bloqueio-section">
                <p class="bloqueio-desc">
                    Defina intervalos <strong>fechados</strong> dentro do horário de funcionamento.<br>
                    Exemplo: almoço das 12:00 às 13:00 todos os dias.
                </p>

                <!-- ── Formulário destacado ── -->
                <div class="bloqueio-form-box">
                    <h3 class="bloqueio-form-title">
                        <i class="fa-solid fa-plus-circle"></i> Novo Bloqueio
                    </h3>
                    <div class="bloqueio-form-grid">
                        <div class="bloqueio-form-field">
                            <label class="bloqueio-form-label" for="bloqueio-dia">Dia da semana</label>
                            <select id="bloqueio-dia" class="horario-input bloqueio-select">
                                <option value="">Todos os dias</option>
                                <?php foreach ($nomes as $num => $nome): ?>
                                    <option value="<?= $num ?>"><?= htmlspecialchars($nome) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="bloqueio-form-field">
                            <label class="bloqueio-form-label" for="bloqueio-inicio">Início do bloqueio</label>
                            <input type="time" id="bloqueio-inicio" class="horario-input" step="1800" />
                        </div>
                        <div class="bloqueio-form-field">
                            <label class="bloqueio-form-label" for="bloqueio-fim">Fim do bloqueio</label>
                            <input type="time" id="bloqueio-fim" class="horario-input" step="1800" />
                        </div>
                        <div class="bloqueio-form-field bloqueio-form-field--wide">
                            <label class="bloqueio-form-label" for="bloqueio-desc">Descrição <span style="font-weight:400;opacity:.7">(opcional)</span></label>
                            <input type="text" id="bloqueio-desc" class="horario-input" style="width:100%" placeholder="Ex: Almoço, Pausa, Limpeza..." maxlength="100" />
                        </div>
                        <div class="bloqueio-form-field bloqueio-form-field--wide" id="bloqueio-excecao-wrap">
                            <label class="bloqueio-form-label">Exceto nos dias <span style="font-weight:400;opacity:.7">(o bloqueio não vale nesses dias)</span></label>
                            <div class="bloqueio-dias-chips">
                                <?php foreach ($abrevDias as $num => $ab): ?>
                                    <label class="dia-chip"><input type="checkbox" class="bloqueio-excecao-dia" value="<?= $num ?>"> <span><?= htmlspecialchars($ab) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-bloqueio-criar" id="btn-bloqueio-add">
                        <i class="fa-solid fa-plus"></i> Criar Bloqueio
                    </button>
                </div>

                <!-- ── Lista de bloqueios ativos ── -->
                <div class="bloqueio-lista-titulo">
                    <i class="fa-solid fa-list"></i> Bloqueios configurados
                </div>
                <div class="table-wrapper">
                <table class="dash-table horarios-table" id="tbl-bloqueios">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Descrição</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-bloqueios-body">
                        <?php if (empty($bloqueios)): ?>
                            <tr id="bloqueio-empty">
                                <td colspan="5" class="table-empty-cell">Nenhum bloqueio configurado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bloqueios as $b):
                                if ($b['dia_semana']) {
                                    $labelDia = htmlspecialchars($nomes[$b['dia_semana']] ?? '—');
                                } elseif (!empty($b['dias_excecao'])) {
                                    $abrevs   = array_map(static fn($d) => $abrevDias[$d] ?? $d, $b['dias_excecao']);
                                    $labelDia = '<em>Todos os dias</em> <small style="opacity:.75">(exceto ' . htmlspecialchars(implode(', ', $abrevs)) . ')</small>';
                                } else {
                                    $labelDia = '<em>Todos os dias</em>';
                                }
                            ?>
                                <tr data-bloqueio-id="<?= (int) $b['id'] ?>"
                                    data-dia="<?= $b['dia_semana'] !== null ? (int) $b['dia_semana'] : '' ?>"
                                    data-excecao="<?= htmlspecialchars(implode(',', $b['dias_excecao'] ?? [])) ?>"
                                    data-inicio="<?= htmlspecialchars(substr($b['hora_inicio'], 0, 5)) ?>"
                                    data-fim="<?= htmlspecialchars(substr($b['hora_fim'], 0, 5)) ?>"
                                    data-descricao="<?= htmlspecialchars($b['descricao'] ?? '') ?>">
                                    <td><?= $labelDia ?></td>
                                    <td><?= htmlspecialchars(substr($b['hora_inicio'], 0, 5)) ?></td>
                                    <td><?= htmlspecialchars(substr($b['hora_fim'],    0, 5)) ?></td>
                                    <td><?= htmlspecialchars($b['descricao'] ?? '—') ?></td>
                                    <td>
                                        <button type="button" class="btn-action btn-action--edit btn-bloqueio-editar" data-id="<?= (int) $b['id'] ?>" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-action--delete btn-bloqueio-excluir" data-id="<?= (int) $b['id'] ?>" title="Remover">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ── Bloqueios por período (férias) ── -->
        <div class="dashboard-card horarios-card" id="card-periodos">
            <div class="dashboard-card-header">
                <h2 class="dashboard-card-title">
                    <i class="fa-solid fa-umbrella-beach"></i> Bloqueios por Período
                </h2>
            </div>

            <div class="bloqueio-section">
                <p class="bloqueio-desc">
                    Feche a barbearia por um <strong>intervalo de datas</strong> — ex.: férias, reforma, feriado prolongado.<br>
                    Marque <strong>dia inteiro</strong> para fechar os dias por completo, ou informe uma faixa para bloquear só um horário em cada dia.
                    Precisa abrir um dia dentro do período? Use a <strong>exceção por data</strong> na agenda (ela tem prioridade).
                </p>

                <!-- ── Formulário ── -->
                <div class="bloqueio-form-box">
                    <h3 class="bloqueio-form-title">
                        <i class="fa-solid fa-plus-circle"></i> <span id="periodo-form-titulo">Novo Período</span>
                    </h3>
                    <input type="hidden" id="periodo-id" value="">
                    <div class="bloqueio-form-grid">
                        <div class="bloqueio-form-field">
                            <label class="bloqueio-form-label" for="periodo-inicio">Data inicial</label>
                            <input type="date" id="periodo-inicio" class="horario-input" lang="pt-BR" />
                        </div>
                        <div class="bloqueio-form-field">
                            <label class="bloqueio-form-label" for="periodo-fim">Data final</label>
                            <input type="date" id="periodo-fim" class="horario-input" lang="pt-BR" />
                        </div>
                        <div class="bloqueio-form-field bloqueio-form-field--wide">
                            <label class="horario-toggle" style="margin-top:22px">
                                <input type="checkbox" id="periodo-dia-inteiro" checked />
                                <span class="horario-toggle__track"></span>
                                <span class="horario-toggle__label">Fechar o dia inteiro</span>
                            </label>
                        </div>
                        <div class="bloqueio-form-field periodo-horas" hidden>
                            <label class="bloqueio-form-label" for="periodo-hora-inicio">Início do bloqueio</label>
                            <input type="time" id="periodo-hora-inicio" class="horario-input" step="1800" />
                        </div>
                        <div class="bloqueio-form-field periodo-horas" hidden>
                            <label class="bloqueio-form-label" for="periodo-hora-fim">Fim do bloqueio</label>
                            <input type="time" id="periodo-hora-fim" class="horario-input" step="1800" />
                        </div>
                        <div class="bloqueio-form-field bloqueio-form-field--wide">
                            <label class="bloqueio-form-label" for="periodo-desc">Descrição <span style="font-weight:400;opacity:.7">(opcional)</span></label>
                            <input type="text" id="periodo-desc" class="horario-input" style="width:100%" placeholder="Ex: Férias, Reforma, Feriado..." maxlength="150" />
                        </div>
                    </div>
                    <button type="button" class="btn-bloqueio-criar" id="btn-periodo-add">
                        <i class="fa-solid fa-plus"></i> <span id="periodo-btn-label">Criar Período</span>
                    </button>
                    <button type="button" class="btn-primary btn-primary--compact" id="btn-periodo-cancelar" hidden style="margin-left:8px">
                        Cancelar edição
                    </button>
                </div>

                <!-- ── Lista de períodos ── -->
                <div class="bloqueio-lista-titulo">
                    <i class="fa-solid fa-list"></i> Períodos configurados
                </div>
                <div class="table-wrapper">
                <table class="dash-table horarios-table" id="tbl-periodos">
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th>Bloqueio</th>
                            <th>Descrição</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-periodos-body">
                        <?php if (empty($periodos)): ?>
                            <tr id="periodo-empty">
                                <td colspan="4" class="table-empty-cell">Nenhum período configurado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($periodos as $p):
                                $diaInteiro = $p['hora_inicio'] === null;
                                $hi = $diaInteiro ? '' : substr($p['hora_inicio'], 0, 5);
                                $hf = $diaInteiro ? '' : substr($p['hora_fim'], 0, 5);
                                $labelIntervalo = $p['data_inicio'] === $p['data_fim']
                                    ? date('d/m/Y', strtotime($p['data_inicio']))
                                    : date('d/m/Y', strtotime($p['data_inicio'])) . ' → ' . date('d/m/Y', strtotime($p['data_fim']));
                            ?>
                                <tr data-periodo-id="<?= (int) $p['id'] ?>"
                                    data-inicio="<?= htmlspecialchars($p['data_inicio']) ?>"
                                    data-fim="<?= htmlspecialchars($p['data_fim']) ?>"
                                    data-dia-inteiro="<?= $diaInteiro ? '1' : '0' ?>"
                                    data-hora-inicio="<?= htmlspecialchars($hi) ?>"
                                    data-hora-fim="<?= htmlspecialchars($hf) ?>"
                                    data-descricao="<?= htmlspecialchars($p['descricao'] ?? '') ?>">
                                    <td><?= htmlspecialchars($labelIntervalo) ?></td>
                                    <td><?= $diaInteiro ? '<em>Dia inteiro</em>' : htmlspecialchars($hi . '–' . $hf) ?></td>
                                    <td><?= htmlspecialchars($p['descricao'] ?? '—') ?></td>
                                    <td>
                                        <button type="button" class="btn-action btn-action--edit btn-periodo-editar" data-id="<?= (int) $p['id'] ?>" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-action--delete btn-periodo-excluir" data-id="<?= (int) $p['id'] ?>" title="Remover">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </main>
</div>

<?php $pageScripts = ['horarios.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
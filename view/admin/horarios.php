<?php
require_once __DIR__ . '/../../api/auth/require_admin.php';
require_once __DIR__ . '/../../sql/HorariosSql.php';

$activePage = 'horarios';
$pageTitle  = 'Horários de Funcionamento';
$horarios   = HorariosSql::buscarTodos();
$nomes      = HorariosSql::nomesDias();
$bloqueios  = HorariosSql::buscarTodosBloqueios();

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
                                <td>
                                    <input
                                        type="time"
                                        class="horario-input horario-abertura"
                                        value="<?= htmlspecialchars($abertura) ?>"
                                        <?= $fechado ? 'disabled' : '' ?>
                                        step="1800" />
                                </td>
                                <td>
                                    <input
                                        type="time"
                                        class="horario-input horario-fechamento"
                                        value="<?= htmlspecialchars($fecho) ?>"
                                        <?= $fechado ? 'disabled' : '' ?>
                                        step="1800" />
                                </td>
                                <td>
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
                    </div>
                    <button type="button" class="btn-bloqueio-criar" id="btn-bloqueio-add">
                        <i class="fa-solid fa-plus"></i> Criar Bloqueio
                    </button>
                </div>

                <!-- ── Lista de bloqueios ativos ── -->
                <div class="bloqueio-lista-titulo">
                    <i class="fa-solid fa-list"></i> Bloqueios configurados
                </div>
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
                            <?php foreach ($bloqueios as $b): ?>
                                <tr data-bloqueio-id="<?= (int) $b['id'] ?>">
                                    <td><?= $b['dia_semana'] ? htmlspecialchars($nomes[$b['dia_semana']] ?? '—') : '<em>Todos os dias</em>' ?></td>
                                    <td><?= htmlspecialchars(substr($b['hora_inicio'], 0, 5)) ?></td>
                                    <td><?= htmlspecialchars(substr($b['hora_fim'],    0, 5)) ?></td>
                                    <td><?= htmlspecialchars($b['descricao'] ?? '—') ?></td>
                                    <td>
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

    </main>
</div>

<?php $pageScripts = ['horarios.js'];
include __DIR__ . '/../partials/scripts.php'; ?>
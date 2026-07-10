<?php

/**
 * Modal de agendamento (admin) — Criar / Editar.
 *
 * Variáveis:
 *   $modal_id           — default 'modal-agendamento'
 *   $modal_confirm_text — default 'Salvar'
 *   $modal_show_status  — bool, exibe campo Status (default false)
 */
$modal_id           = $modal_id           ?? 'modal-agendamento';
$modal_confirm_text = $modal_confirm_text ?? 'Salvar';
$modal_show_status  = $modal_show_status  ?? false;
?>
<div class="modal-overlay" id="<?= htmlspecialchars($modal_id) ?>">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Agendamento</h2>
            <button class="modal-close" data-close="<?= htmlspecialchars($modal_id) ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Cliente</label>
                    <select class="modal-select" name="usuario_id" data-field="usuario_id">
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes ?? [] as $c): ?>
                            <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['nome'] . ' ' . $c['sobrenome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Serviços</label>
                    <div class="agendamento-servicos" data-agendamento-servicos>
                        <?php foreach ($servicos ?? [] as $s): ?>
                            <label class="agendamento-servico-opcao">
                                <input type="checkbox" name="servicos_ids" value="<?= (int) $s['id'] ?>"
                                    data-preco="<?= htmlspecialchars((string) $s['preco']) ?>"
                                    data-duracao="<?= (int) $s['tempo_estimado'] ?>">
                                <span><?= htmlspecialchars($s['nome']) ?></span>
                                <small>R$ <?= number_format($s['preco'], 2, ',', '.') ?> · <?= (int) $s['tempo_estimado'] ?> min</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-field modal-summary" data-admin-agendamento-resumo hidden>
                    <p class="modal-label modal-label--compact">Resumo</p>
                    <small class="modal-summary__details"><span data-admin-agendamento-lista></span><br>Total: <strong data-admin-agendamento-total></strong> · Duração: <strong data-admin-agendamento-duracao></strong> · Término: <strong data-admin-agendamento-fim></strong></small>
                </div>
                <div class="modal-field modal-selected-time" data-horario-selecionado hidden>
                    <label class="modal-label">Horário escolhido</label>
                    <p class="modal-input modal-selected-time__value" data-horario-selecionado-texto></p>
                </div>
                <div class="modal-row" data-periodo-campos>
                    <div class="modal-field">
                        <label class="modal-label">Data</label>
                        <input class="modal-input" type="date" lang="pt-BR" name="data" data-field="data" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Horário</label>
                        <input class="modal-input" type="time" name="hora_inicio" data-field="hora_inicio" />
                    </div>
                </div>
                <input type="hidden" name="hora_fim" value="" />
                <div class="modal-field">
                    <label class="modal-label">Observação <small>(opcional)</small></label>
                    <textarea class="modal-textarea" name="observacoes" rows="3" maxlength="1000" placeholder="Detalhes importantes sobre este agendamento"></textarea>
                </div>
                <?php if ($modal_show_status): ?>
                    <div class="modal-field">
                        <label class="modal-label">Status</label>
                        <select class="modal-select" name="status" data-field="status">
                            <option value="confirmado">Confirmado</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="<?= htmlspecialchars($modal_id) ?>">Cancelar</button>
            <button class="btn-modal-primary"><?= htmlspecialchars($modal_confirm_text) ?></button>
        </div>
    </div>
</div>

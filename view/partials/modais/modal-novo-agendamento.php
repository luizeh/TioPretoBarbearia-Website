<?php

/** Modal de novo agendamento — área do cliente. Requer $servicos no escopo. */ ?>
<div class="modal-overlay" id="modal-novo-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Novo Agendamento</h2>
            <button class="modal-close" data-close="modal-novo-agendamento"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Serviço</label>
                    <select class="modal-select" name="servico_id" id="novoag-servico">
                        <option value="">Selecione um serviço</option>
                        <?php foreach ($servicos ?? [] as $s): ?>
                            <option value="<?= $s['id'] ?>" data-duracao="<?= (int) $s['tempo_estimado'] ?>">
                                <?= htmlspecialchars($s['nome']) ?> — R$ <?= number_format($s['preco'], 2, ',', '.') ?> (<?= (int) $s['tempo_estimado'] ?> min)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data</label>
                        <input class="modal-input" type="date" name="data" id="novoag-data" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Horário de Início</label>
                        <input class="modal-input" type="time" name="hora_inicio" id="novoag-hora-inicio" step="1800" />
                    </div>
                </div>
                <input type="hidden" name="hora_fim" id="novoag-hora-fim" />
                <div class="modal-field" id="novoag-duracao-info" style="display:none;">
                    <small style="color:var(--gold);font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;">
                        <i class="fa-regular fa-clock"></i> Término estimado: <strong id="novoag-hora-fim-display">—</strong>
                    </small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-novo-agendamento">Cancelar</button>
            <button class="btn-modal-primary" id="novoag-submit">Confirmar Agendamento</button>
        </div>
    </div>
</div>
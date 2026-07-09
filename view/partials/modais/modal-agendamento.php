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
                    <input class="modal-input" type="text" placeholder="Nome do cliente" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Serviço</label>
                    <select class="modal-select">
                        <option value="">Selecione um serviço</option>
                        <option>Corte Social</option>
                        <option>Corte + Barba</option>
                        <option>Barba Degradê</option>
                        <option>Hidratação</option>
                        <option>Sobrancelha</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data</label>
                        <input class="modal-input" type="date" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Horário</label>
                        <input class="modal-input" type="time" />
                    </div>
                </div>
                <?php if ($modal_show_status): ?>
                    <div class="modal-field">
                        <label class="modal-label">Status</label>
                        <select class="modal-select">
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
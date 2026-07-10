<?php

/**
 * Modal de visualização de agendamento (admin) — somente leitura.
 * Campos populados via data-field pelo swal-modals.js.
 */
?>
<div class="modal-overlay" id="modal-agendamento-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-check"></i> Detalhes do Agendamento</h2>
            <button class="modal-close" data-close="modal-agendamento-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Cliente</dt>
                <dd data-field="cliente">—</dd>
                <dt>Serviço</dt>
                <dd data-field="servico">—</dd>
                <dt>Data</dt>
                <dd data-field="data">—</dd>
                <dt>Horário</dt>
                <dd data-field="horario">—</dd>
                <dt>Status</dt>
                <dd data-field="status">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-agendamento-ver">Fechar</button>
        </div>
    </div>
</div>
<?php

/** Modal de confirmação de cancelamento de agendamento — área do cliente. */ ?>
<div class="modal-overlay" id="modal-cancelar-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-xmark"></i> Cancelar Agendamento</h2>
            <button class="modal-close" data-close="modal-cancelar-agendamento"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="modal-delete-text">
                Tem certeza que deseja cancelar o agendamento de
                <strong data-field="servico">este serviço</strong>?
                <span class="modal-delete-hint">Esta ação não pode ser desfeita.</span>
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cancelar-agendamento">Voltar</button>
            <button class="btn-modal-danger">Confirmar Cancelamento</button>
        </div>
    </div>
</div>
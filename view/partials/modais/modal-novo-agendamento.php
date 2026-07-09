<?php

/** Modal de novo agendamento — área do cliente (público). */ ?>
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
                    <select class="modal-select">
                        <option value="">Selecione um serviço</option>
                        <option>Corte Social — R$ 35,00 (30 min)</option>
                        <option>Corte + Barba — R$ 55,00 (60 min)</option>
                        <option>Barba Degradê — R$ 40,00 (45 min)</option>
                        <option>Hidratação — R$ 45,00 (40 min)</option>
                        <option>Sobrancelha — R$ 20,00 (15 min)</option>
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
                <div class="modal-field">
                    <label class="modal-label">Observação (opcional)</label>
                    <input class="modal-input" type="text" placeholder="Ex: preferência de barbeiro" />
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-novo-agendamento">Cancelar</button>
            <button class="btn-modal-primary">Confirmar Agendamento</button>
        </div>
    </div>
</div>
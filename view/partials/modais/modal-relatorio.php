<?php

/** Modal de geração de relatório. */ ?>
<div class="modal-overlay" id="modal-relatorio">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-file-invoice-dollar"></i> Gerar Relatório</h2>
            <button class="modal-close" data-close="modal-relatorio"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Tipo de Relatório</label>
                    <select class="modal-select">
                        <option value="">Selecione o tipo</option>
                        <option>Agendamentos</option>
                        <option>Receita</option>
                        <option>Clientes</option>
                        <option>Serviços Mais Vendidos</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data Início</label>
                        <input class="modal-input" type="date" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Data Fim</label>
                        <input class="modal-input" type="date" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Formato</label>
                    <select class="modal-select">
                        <option>PDF</option>
                        <option>Excel (.xlsx)</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-relatorio">Cancelar</button>
            <button class="btn-modal-primary">Gerar</button>
        </div>
    </div>
</div>
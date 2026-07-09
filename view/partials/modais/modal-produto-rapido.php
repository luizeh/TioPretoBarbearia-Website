<?php

/**
 * Modal rápido de produto — versão simplificada para o dashboard.
 * Sem dropzone de foto nem tag picker.
 */
?>
<div class="modal-overlay" id="modal-produto">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Adicionar Produto</h2>
            <button class="modal-close" data-close="modal-produto"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Nome do Produto</label>
                    <input class="modal-input" type="text" placeholder="Ex: Pomada Modeladora" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Categoria</label>
                    <select class="modal-select">
                        <option value="">Selecione uma categoria</option>
                        <option>Finalizador</option>
                        <option>Shampoo</option>
                        <option>Condicionador</option>
                        <option>Óleo para Barba</option>
                        <option>Outros</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Preço (R$)</label>
                        <input class="modal-input" type="number" min="0" step="0.01" placeholder="0,00" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Estoque (un)</label>
                        <input class="modal-input" type="number" min="0" placeholder="0" />
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-produto">Cancelar</button>
            <button class="btn-modal-primary">Adicionar</button>
        </div>
    </div>
</div>
<?php

/**
 * Modal de tag — Criar / Editar.
 *
 * Variáveis:
 *   $modal_id           — default 'modal-tag'
 *   $modal_confirm_text — default 'Salvar'
 */
$modal_id           = $modal_id           ?? 'modal-tag';
$modal_confirm_text = $modal_confirm_text ?? 'Salvar';
?>
<div class="modal-overlay" id="<?= htmlspecialchars($modal_id) ?>">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-tag"></i> Tag</h2>
            <button class="modal-close" data-close="<?= htmlspecialchars($modal_id) ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Nome da Tag</label>
                    <input class="modal-input" type="text" data-field="nome" placeholder="Ex: Cabelo" />
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="<?= htmlspecialchars($modal_id) ?>">Cancelar</button>
            <button class="btn-modal-primary"><?= htmlspecialchars($modal_confirm_text) ?></button>
        </div>
    </div>
</div>
<?php

/**
 * Modal genérico de confirmação de exclusão.
 *
 * Variáveis esperadas:
 *   $modal_id           — id do overlay  (ex: 'modal-cliente-excluir')
 *   $modal_title        — texto do título (ex: 'Excluir Cliente')
 *   $modal_entity_label — texto da entidade (ex: 'este cliente')
 */
$modal_id           = $modal_id           ?? 'modal-excluir';
$modal_title        = $modal_title        ?? 'Excluir';
$modal_entity_label = $modal_entity_label ?? 'este item';
?>
<div class="modal-overlay" id="<?= htmlspecialchars($modal_id) ?>">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-trash"></i> <?= htmlspecialchars($modal_title) ?></h2>
            <button class="modal-close" data-close="<?= htmlspecialchars($modal_id) ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="modal-delete-text">
                Tem certeza que deseja excluir <strong data-field="nome"><?= htmlspecialchars($modal_entity_label) ?></strong>?
                <span class="modal-delete-hint">Esta ação não pode ser desfeita.</span>
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="<?= htmlspecialchars($modal_id) ?>">Cancelar</button>
            <button class="btn-modal-danger">Excluir</button>
        </div>
    </div>
</div>
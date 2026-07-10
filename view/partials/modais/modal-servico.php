<?php

/**
 * Modal de formulário de serviço — Criar / Editar.
 *
 * Variáveis:
 *   $modal_id           — default 'modal-servico'
 *   $modal_confirm_text — default 'Salvar'
 *   $modal_use_fields   — bool, adiciona data-field nos inputs (default false)
 */
$modal_id           = $modal_id           ?? 'modal-servico';
$modal_confirm_text = $modal_confirm_text ?? 'Salvar';
$modal_use_fields   = $modal_use_fields   ?? false;

function _df_s(string $field, bool $use): string
{
    return $use ? ' data-field="' . $field . '"' : '';
}
?>
<div class="modal-overlay" id="<?= htmlspecialchars($modal_id) ?>">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-scissors"></i> Serviço</h2>
            <button class="modal-close" data-close="<?= htmlspecialchars($modal_id) ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Foto do Serviço</label>
                    <div class="foto-dropzone" id="foto-dropzone">
                        <img class="foto-dropzone__preview" id="foto-preview" src="" alt="Preview" />
                        <div class="foto-dropzone__placeholder" id="foto-placeholder">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Arraste ou clique para enviar</span>
                            <small>JPG, PNG, WebP · máx. 2MB</small>
                        </div>
                        <button type="button" class="foto-dropzone__remove" id="foto-remove" title="Remover foto">Remover</button>
                        <input type="file" id="foto-file-input" accept="image/jpeg,image/png,image/webp,image/gif" />
                        <input type="hidden" data-field="foto_url" id="foto-url-hidden" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Nome do Serviço</label>
                    <input class="modal-input" type="text" <?= _df_s('nome', $modal_use_fields) ?> placeholder="Ex: Corte Social" />
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Duração (min)</label>
                        <input class="modal-input" type="number" <?= _df_s('duracao', $modal_use_fields) ?> placeholder="30" min="5" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Preço (R$)</label>
                        <input class="modal-input" type="number" <?= _df_s('preco', $modal_use_fields) ?> placeholder="35.00" step="0.01" min="0" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Descrição</label>
                    <textarea class="modal-textarea" <?= _df_s('descricao', $modal_use_fields) ?> placeholder="Descreva o serviço..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="<?= htmlspecialchars($modal_id) ?>">Cancelar</button>
            <button class="btn-modal-primary"><?= htmlspecialchars($modal_confirm_text) ?></button>
        </div>
    </div>
</div>

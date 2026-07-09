<?php

/** Modal completo de produto (com dropzone de foto e tag picker) — Criar / Editar. */ ?>
<div class="modal-overlay" id="modal-produto">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Produto</h2>
            <button class="modal-close" data-close="modal-produto"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Foto do Produto</label>
                    <div class="foto-dropzone" id="foto-dropzone">
                        <img class="foto-dropzone__preview" id="foto-preview" src="" alt="Preview" />
                        <div class="foto-dropzone__placeholder" id="foto-placeholder">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Arraste ou clique para enviar</span>
                            <small>JPG, PNG, WebP · máx. 2MB</small>
                        </div>
                        <button type="button" class="foto-dropzone__remove" id="foto-remove" title="Remover foto">✕ Remover</button>
                        <input type="file" id="foto-file-input" accept="image/jpeg,image/png,image/webp,image/gif" />
                        <input type="hidden" data-field="fotoUrl" id="foto-url-hidden" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Nome do Produto</label>
                    <input class="modal-input" type="text" data-field="nome" placeholder="Ex: Pomada Matte" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Tags</label>
                    <div class="tag-picker" id="tag-picker">

                        <?php foreach ($tags ?? [] as $tag): ?>

                            <button type="button"
                                class="tag-option"
                                data-tag-id="<?= $tag['id'] ?>">
                                <?= htmlspecialchars($tag['nome']) ?>
                            </button>

                        <?php endforeach; ?>

                    </div>
                </div>
                <input type="hidden" data-field="tagIds" id="tag-hidden" name="tags" />

                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Estoque (un)</label>
                        <input class="modal-input" type="number" data-field="estoque" placeholder="0" min="0" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Preço Unit. (R$)</label>
                        <input class="modal-input" type="number" data-field="preco" placeholder="0.00" step="0.01" min="0" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Descrição</label>
                    <textarea class="modal-textarea" data-field="descricao" placeholder="Descreva o produto..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-produto">Cancelar</button>
            <button class="btn-modal-primary">Salvar</button>
        </div>
    </div>
</div>
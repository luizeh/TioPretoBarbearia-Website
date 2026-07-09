<?php

/** Modal de visualização de produto — somente leitura. */ ?>
<div class="modal-overlay" id="modal-produto-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-box-open"></i> Detalhes do Produto</h2>
            <button class="modal-close" data-close="modal-produto-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Produto</dt>
                <dd data-field="nome">—</dd>
                <dt>Foto</dt>
                <dd>
                    <img id="ver-foto-img" src="" alt="Foto do produto"
                        style="max-width:100%;max-height:200px;border-radius:4px;display:none;" />
                    <span id="ver-foto-vazio" style="color:#888;font-size:0.85rem;">Sem foto</span>
                </dd>
                <dt>Descrição</dt>
                <dd data-field="descricao">—</dd>
                <dt>Tags</dt>
                <dd data-field="tags">—</dd>
                <dt>Estoque</dt>
                <dd data-field="estoque">—</dd>
                <dt>Preço</dt>
                <dd data-field="preco">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-produto-ver">Fechar</button>
        </div>
    </div>
</div>
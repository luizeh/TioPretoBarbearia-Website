<?php

/** Modal de visualização de serviço — somente leitura. */ ?>
<div class="modal-overlay" id="modal-servico-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-scissors"></i> Detalhes do Serviço</h2>
            <button class="modal-close" data-close="modal-servico-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Serviço</dt>
                <dd data-field="nome">—</dd>
                <dt>Duração</dt>
                <dd data-field="duracao">—</dd>
                <dt>Preço</dt>
                <dd data-field="preco">—</dd>
                <dt>Descrição</dt>
                <dd data-field="descricao">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-servico-ver">Fechar</button>
        </div>
    </div>
</div>
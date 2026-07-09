<?php

/**
 * Modal de visualização de cliente — somente leitura.
 */
?>
<div class="modal-overlay" id="modal-cliente-ver">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-user"></i> Dados do Cliente</h2>
            <button class="modal-close" data-close="modal-cliente-ver"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <dl class="modal-info">
                <dt>Nome</dt>
                <dd data-field="nome">—</dd>
                <dt>E-mail</dt>
                <dd data-field="email">—</dd>
                <dt>Telefone</dt>
                <dd data-field="telefone">—</dd>
                <dt>Cidade</dt>
                <dd data-field="cidade">—</dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-cliente-ver">Fechar</button>
        </div>
    </div>
</div>
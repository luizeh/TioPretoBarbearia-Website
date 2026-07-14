<?php

/**
 * Modal de formulário de cliente — Cadastrar (dashboard) ou Editar (clientes).
 *
 * Variáveis:
 *   $modal_id           — id do overlay (ex: 'modal-cliente' ou 'modal-cliente-editar')
 *   $modal_title        — texto do título (ex: 'Cadastrar Cliente')
 *   $modal_confirm_text — texto do botão primário (ex: 'Cadastrar' ou 'Salvar')
 *   $modal_use_fields   — bool, se true adiciona data-field nos inputs (default false)
 */
$modal_id           = $modal_id           ?? 'modal-cliente';
$modal_title        = $modal_title        ?? 'Cliente';
$modal_confirm_text = $modal_confirm_text ?? 'Salvar';
$modal_use_fields   = $modal_use_fields   ?? false;
$modal_show_senha   = $modal_show_senha   ?? false;

if (!function_exists('_df')) {
    function _df(string $field, bool $use): string
    {
        return $use ? ' data-field="' . $field . '"' : '';
    }
}
?>
<div class="modal-overlay" id="<?= htmlspecialchars($modal_id) ?>">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($modal_title) ?></h2>
            <button class="modal-close" data-close="<?= htmlspecialchars($modal_id) ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Nome</label>
                        <input class="modal-input" type="text" <?= _df('nome', $modal_use_fields) ?> placeholder="Nome" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Sobrenome</label>
                        <input class="modal-input" type="text" <?= _df('sobrenome', $modal_use_fields) ?> placeholder="Sobrenome" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">E-mail</label>
                    <input class="modal-input" type="email" <?= _df('email', $modal_use_fields) ?> placeholder="email@exemplo.com" />
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Telefone</label>
                        <input class="modal-input" type="tel" <?= _df('telefone', $modal_use_fields) ?> placeholder="+55 (00) 00000-0000" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Cidade</label>
                        <input class="modal-input" type="text" <?= _df('cidade', $modal_use_fields) ?> placeholder="Cidade" />
                    </div>
                </div>
                <?php if ($modal_show_senha): ?>
                    <div class="modal-field" style="margin-top:8px">
                        <label class="modal-label">Senha de acesso</label>
                        <label style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--text-secondary,#94a3b8);margin-bottom:6px;cursor:pointer">
                            <input type="checkbox" data-field="senha-padrao" checked style="width:16px;height:16px;cursor:pointer" />
                            Usar senha padrão <code style="background:var(--surface-2,#1e293b);padding:1px 6px;border-radius:4px">12345678</code>
                        </label>
                        <input class="modal-input" type="password" <?= _df('senha', $modal_use_fields) ?> placeholder="Digite a senha personalizada" style="display:none" />
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="<?= htmlspecialchars($modal_id) ?>">Cancelar</button>
            <button class="btn-modal-primary"><?= htmlspecialchars($modal_confirm_text) ?></button>
        </div>
    </div>
</div>
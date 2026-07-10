<?php
include_once __DIR__ . '/../../api/auth/session.php';

$rootPath = '../../';
$linkBase = '../';
$activeNav = '';
$pageTitle = 'Meu Perfil - Tio Preto Barbearia';
$bodyClass = 'user-page';

include_once __DIR__ . '/../partials/head_public.php';
include_once __DIR__ . '/../partials/header_public.php';
?>

<div class="page-banner">
    <span class="page-banner__eyebrow">Área do Cliente</span>
    <h1 class="page-banner__title">Meu <span>Perfil</span></h1>
    <p class="page-banner__desc">Gerencie suas informações pessoais.</p>
</div>

<main class="profile-page user-agenda">
    <section class="appt-card profile-card">
        <h2 class="profile-card__title">
            <i class="fa-solid fa-user profile-card__icon"></i>
            Dados Pessoais
        </h2>

        <form id="form-perfil" class="modal-form">
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label" for="perfil-nome">Nome</label>
                    <input class="modal-input" type="text" id="perfil-nome" name="nome"
                        value="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>" required />
                </div>
                <div class="modal-field">
                    <label class="modal-label" for="perfil-sobrenome">Sobrenome</label>
                    <input class="modal-input" type="text" id="perfil-sobrenome" name="sobrenome" required />
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label" for="perfil-telefone">Telefone</label>
                    <input class="modal-input" type="tel" id="perfil-telefone" name="telefone" />
                </div>
                <div class="modal-field">
                    <label class="modal-label" for="perfil-cidade">Cidade</label>
                    <input class="modal-input" type="text" id="perfil-cidade" name="cidade" required />
                </div>
            </div>
            <div class="modal-field profile-card__email-field">
                <label class="modal-label" for="perfil-email">E-mail</label>
                <input class="modal-input" type="email" id="perfil-email" name="email" required />
            </div>
            <button type="submit" class="btn-new-appt profile-card__submit">
                <i class="fa-solid fa-floppy-disk"></i>
                Salvar Alterações
            </button>
        </form>
    </section>

    <section class="appt-card profile-card profile-card--danger">
        <h2 class="profile-card__title">
            <i class="fa-solid fa-triangle-exclamation profile-card__icon profile-card__icon--danger"></i>
            Excluir minha conta
        </h2>
        <p class="profile-card__description">
            Essa ação remove sua conta, agendamentos, pedidos e dados relacionados. Não será possível desfazê-la.
        </p>
        <button type="button" id="btn-excluir-conta" class="btn-modal-danger profile-card__danger-button">
            <i class="fa-solid fa-user-xmark"></i>
            Excluir minha conta
        </button>
    </section>

    <section class="appt-card profile-card">
        <h2 class="profile-card__title">
            <i class="fa-solid fa-lock profile-card__icon"></i>
            Alterar Senha
        </h2>
        <form id="form-senha" class="modal-form">
            <div class="modal-field">
                <label class="modal-label" for="perfil-senha-atual">Senha Atual</label>
                <input class="modal-input" type="password" name="senha_atual" id="perfil-senha-atual" required />
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label" for="perfil-nova-senha">Nova Senha</label>
                    <input class="modal-input" type="password" name="nova_senha" id="perfil-nova-senha" required minlength="8" />
                </div>
                <div class="modal-field">
                    <label class="modal-label" for="perfil-confirmar">Confirmar Nova Senha</label>
                    <input class="modal-input" type="password" name="confirmar_senha" id="perfil-confirmar" required minlength="8" />
                </div>
            </div>
            <button type="submit" class="btn-new-appt btn-new-appt--muted profile-card__submit">
                <i class="fa-solid fa-key"></i>
                Alterar Senha
            </button>
        </form>
    </section>
</main>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= $rootPath ?>assets/js/public/perfil.js" defer></script>
</body>
</html>

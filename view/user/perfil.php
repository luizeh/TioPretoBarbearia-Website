<?php
include_once(__DIR__ . '/../../api/auth/session.php');

$rootPath  = '../../';
$linkBase  = '../';
$activeNav = '';
$pageTitle = 'Meu Perfil — Tio Preto Barbearia';
$bodyClass = 'user-page';

include_once __DIR__ . '/../partials/head_public.php';
?>
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>

<div class="page-banner">
    <span class="page-banner__eyebrow">✦ Área do Cliente</span>
    <h1 class="page-banner__title">Meu <span>Perfil</span></h1>
    <p class="page-banner__desc">Gerencie suas informações pessoais.</p>
</div>

<div class="user-agenda" style="max-width:620px;">

    <!-- Card: Dados Pessoais -->
    <div class="appt-card" style="flex-direction:column;align-items:stretch;gap:24px;padding:32px 36px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;margin-bottom:4px;">
            <i class="fa-solid fa-user" style="color:var(--gold);margin-right:8px;"></i> Dados Pessoais
        </h2>

        <form id="form-perfil" class="modal-form">
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label">Nome</label>
                    <input class="modal-input" type="text" id="perfil-nome" name="nome"
                        value="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>" required />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Sobrenome</label>
                    <input class="modal-input" type="text" id="perfil-sobrenome" name="sobrenome" required />
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label">Telefone</label>
                    <input class="modal-input" type="tel" id="perfil-telefone" name="telefone" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Cidade</label>
                    <input class="modal-input" type="text" id="perfil-cidade" name="cidade" required />
                </div>
            </div>
            <div class="modal-field" style="margin-top:4px;">
                <label class="modal-label">E-mail <small style="color:var(--gray)">(não editável)</small></label>
                <input class="modal-input" type="email" id="perfil-email" disabled
                    style="opacity:.55;cursor:not-allowed;" />
            </div>
            <button type="submit" class="btn-new-appt" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
            </button>
        </form>
    </div>

    <!-- Card: Alterar Senha -->
    <div class="appt-card" style="flex-direction:column;align-items:stretch;gap:24px;padding:32px 36px;margin-top:20px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;margin-bottom:4px;">
            <i class="fa-solid fa-lock" style="color:var(--gold);margin-right:8px;"></i> Alterar Senha
        </h2>
        <form id="form-senha" class="modal-form">
            <div class="modal-field">
                <label class="modal-label">Senha Atual</label>
                <input class="modal-input" type="password" name="senha_atual" id="perfil-senha-atual" required />
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label class="modal-label">Nova Senha</label>
                    <input class="modal-input" type="password" name="nova_senha" id="perfil-nova-senha" required minlength="8" />
                </div>
                <div class="modal-field">
                    <label class="modal-label">Confirmar Nova Senha</label>
                    <input class="modal-input" type="password" name="confirmar_senha" id="perfil-confirmar" required minlength="8" />
                </div>
            </div>
            <button type="submit" class="btn-new-appt" style="width:100%;justify-content:center;margin-top:8px;background:#444;">
                <i class="fa-solid fa-key"></i> Alterar Senha
            </button>
        </form>
    </div>

</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

<script src="<?= $rootPath ?>assets/js/public.js"></script>
<script src="<?= $rootPath ?>assets/js/cart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var BASE = window.API_BASE || '../../api/';

        // Carregar dados do perfil
        fetch(BASE + 'user/perfil.php')
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                if (!res.success) return;
                var u = res.data;
                document.getElementById('perfil-nome').value = u.nome || '';
                document.getElementById('perfil-sobrenome').value = u.sobrenome || '';
                document.getElementById('perfil-telefone').value = u.telefone || '';
                document.getElementById('perfil-cidade').value = u.cidade || '';
                document.getElementById('perfil-email').value = u.email || '';
            });

        // Salvar dados pessoais
        document.getElementById('form-perfil').addEventListener('submit', function(e) {
            e.preventDefault();
            var dados = {
                action: 'editar',
                nome: document.getElementById('perfil-nome').value.trim(),
                sobrenome: document.getElementById('perfil-sobrenome').value.trim(),
                telefone: document.getElementById('perfil-telefone').value.trim(),
                cidade: document.getElementById('perfil-cidade').value.trim(),
            };
            fetch(BASE + 'user/perfil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            }).then(function(r) {
                return r.json();
            }).then(function(res) {
                if (res.success) {
                    SwalTP.fire({
                        icon: 'success',
                        title: 'Salvo!',
                        text: 'Perfil atualizado.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    SwalTP.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: res.message || 'Não foi possível salvar.'
                    });
                }
            });
        });

        // Alterar senha
        document.getElementById('form-senha').addEventListener('submit', function(e) {
            e.preventDefault();
            var nova = document.getElementById('perfil-nova-senha').value;
            var conf = document.getElementById('perfil-confirmar').value;
            if (nova !== conf) {
                SwalTP.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'As senhas não coincidem.'
                });
                return;
            }
            fetch(BASE + 'user/perfil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'senha',
                    senha_atual: document.getElementById('perfil-senha-atual').value,
                    nova_senha: nova
                })
            }).then(function(r) {
                return r.json();
            }).then(function(res) {
                if (res.success) {
                    SwalTP.fire({
                        icon: 'success',
                        title: 'Senha alterada!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    document.getElementById('form-senha').reset();
                } else {
                    SwalTP.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: res.message || 'Não foi possível alterar.'
                    });
                }
            });
        });
    });
</script>
</body>

</html>
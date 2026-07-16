// Operacoes da pagina de perfil do cliente (dados, contato/verificacao, senha, exclusao).
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var base = window.API_BASE || "../../api/";
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
    var formPerfil = document.getElementById("form-perfil");
    var formSenha = document.getElementById("form-senha");
    var buttonExcluir = document.getElementById("btn-excluir-conta");

    if (!formPerfil || !formSenha) return;

    function request(payload) {
      var opts = { method: payload ? "POST" : "GET" };
      if (payload) {
        opts.headers = { "Content-Type": "application/json", "X-CSRF-Token": csrf };
        opts.body = JSON.stringify(payload);
      }
      return fetch(base + "user/perfil.php", opts).then(function (r) {
        return r.json().catch(function () { return { success: false, message: "Resposta inválida do servidor." }; });
      });
    }

    function formatarTelefone(d) {
      d = (d || "").replace(/\D/g, "");
      if (d.indexOf("55") === 0 && d.length > 11) d = d.slice(2);
      if (d.length < 10) return d || "—";
      var ddd = d.slice(0, 2);
      var resto = d.slice(2);
      return resto.length === 9
        ? "(" + ddd + ") " + resto.slice(0, 5) + "-" + resto.slice(5)
        : "(" + ddd + ") " + resto.slice(0, 4) + "-" + resto.slice(4);
    }

    function pintarBadge(el, verificado) {
      if (!el) return;
      el.className = "contato-badge " + (verificado ? "contato-badge--ok" : "contato-badge--pendente");
      el.innerHTML = verificado
        ? '<i class="fa-solid fa-circle-check"></i> Verificado'
        : '<i class="fa-solid fa-circle-exclamation"></i> Não verificado';
    }

    function pintarPendente(el, valor) {
      if (!el) return;
      if (valor) {
        el.hidden = false;
        el.textContent = "Aguardando confirmação: " + valor;
      } else {
        el.hidden = true;
        el.textContent = "";
      }
    }

    function carregarPerfil() {
      return request().then(function (response) {
        if (!response.success) return;
        var user = response.data || {};
        document.getElementById("perfil-nome").value = user.nome || "";
        document.getElementById("perfil-sobrenome").value = user.sobrenome || "";
        document.getElementById("perfil-cidade").value = user.cidade || "";

        document.getElementById("contato-email").textContent = user.email || "—";
        document.getElementById("contato-telefone").textContent = formatarTelefone(user.telefone);
        pintarBadge(document.getElementById("badge-email"), Number(user.email_verificado) === 1);
        pintarBadge(document.getElementById("badge-telefone"), Number(user.telefone_verificado) === 1);
        pintarPendente(document.getElementById("pendente-email"), user.email_pendente);
        pintarPendente(document.getElementById("pendente-telefone"), user.telefone_pendente ? formatarTelefone(user.telefone_pendente) : "");
      });
    }

    carregarPerfil().catch(function () {
      window.SwalTP.fire({ icon: "error", title: "Erro", text: "Não foi possível carregar seu perfil." });
    });

    // ── Dados básicos ──
    formPerfil.addEventListener("submit", function (event) {
      event.preventDefault();
      request({
        action: "editar",
        nome: document.getElementById("perfil-nome").value.trim(),
        sobrenome: document.getElementById("perfil-sobrenome").value.trim(),
        cidade: document.getElementById("perfil-cidade").value.trim(),
      }).then(function (response) {
        if (response.success) {
          window.SwalTP.sucesso("Salvo!", "Perfil atualizado.");
          return;
        }
        window.SwalTP.erro("Erro", response.message || "Não foi possível salvar.");
      }).catch(function () {
        window.SwalTP.erro("Erro", "Não foi possível salvar o perfil.");
      });
    });

    // ── Troca de e-mail / telefone (pendente até verificar) ──
    function pedirCodigo(tipo) {
      var isEmail = tipo === "email";
      var timer = null;
      window.SwalTP.fire({
        title: "Digite o código",
        text: "Enviamos um código de 6 dígitos" + (isEmail ? " para o novo e-mail." : " pelo WhatsApp."),
        input: "text",
        inputAttributes: { inputmode: "numeric", maxlength: 6, autocomplete: "one-time-code" },
        showCancelButton: true,
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        showLoaderOnConfirm: true,
        footer: '<button type="button" id="swal-reenviar" class="link-button">Reenviar código</button>',
        allowOutsideClick: function () { return !window.Swal.isLoading(); },
        didOpen: function () {
          var b = document.getElementById("swal-reenviar");
          if (!b) return;
          b.addEventListener("click", function () {
            if (b.disabled) return;
            b.disabled = true;
            request({ action: isEmail ? "reenviar_email" : "reenviar_telefone" }).then(function (r) {
              var espera = (r.data && r.data.espera) || 60;
              var restante = r.success ? espera : ((r.data && r.data.espera) || 0);
              b.textContent = r.success ? "Código reenviado" : (r.message || "Aguarde");
              if (restante <= 0) { b.disabled = false; b.textContent = "Reenviar código"; return; }
              if (timer) clearInterval(timer);
              timer = setInterval(function () {
                restante--;
                if (restante <= 0) { clearInterval(timer); b.disabled = false; b.textContent = "Reenviar código"; }
                else b.textContent = "Aguarde " + restante + "s";
              }, 1000);
            }).catch(function () { b.disabled = false; });
          });
        },
        preConfirm: function (codigo) {
          var payload = { action: isEmail ? "confirmar_email" : "confirmar_telefone", codigo: (codigo || "").replace(/\D/g, "") };
          return request(payload).then(function (r) {
            if (!r.success) window.Swal.showValidationMessage(r.message || "Código inválido.");
            return r;
          }).catch(function () {
            window.Swal.showValidationMessage("Erro de conexão.");
          });
        },
      }).then(function (res) {
        if (timer) clearInterval(timer);
        if (!res.isConfirmed || !res.value || !res.value.success) return;
        window.SwalTP.sucesso("Pronto!", res.value.message);
        carregarPerfil();
      });
    }

    function fluxoTroca(tipo) {
      var isEmail = tipo === "email";
      window.SwalTP.fire({
        title: isEmail ? "Alterar e-mail" : "Alterar telefone",
        input: isEmail ? "email" : "tel",
        inputLabel: isEmail ? "Novo e-mail" : "Novo telefone",
        inputPlaceholder: isEmail ? "novo@email.com" : "(00) 0 0000-0000",
        showCancelButton: true,
        confirmButtonText: "Enviar código",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        showLoaderOnConfirm: true,
        allowOutsideClick: function () { return !window.Swal.isLoading(); },
        preConfirm: function (valor) {
          var payload = { action: isEmail ? "solicitar_email" : "solicitar_telefone" };
          payload[isEmail ? "novo_email" : "novo_telefone"] = (valor || "").trim();
          return request(payload).then(function (r) {
            if (!r.success) window.Swal.showValidationMessage(r.message || "Não foi possível enviar o código.");
            return r;
          }).catch(function () {
            window.Swal.showValidationMessage("Erro de conexão.");
          });
        },
      }).then(function (res) {
        if (!res.isConfirmed || !res.value || !res.value.success) return;
        pedirCodigo(tipo);
      });
    }

    var btnEmail = document.getElementById("btn-alterar-email");
    var btnTelefone = document.getElementById("btn-alterar-telefone");
    if (btnEmail) btnEmail.addEventListener("click", function () { fluxoTroca("email"); });
    if (btnTelefone) btnTelefone.addEventListener("click", function () { fluxoTroca("telefone"); });

    // ── Excluir conta ──
    if (buttonExcluir) {
      buttonExcluir.addEventListener("click", function () {
        window.SwalTP.confirmarExclusao({
          title: "Excluir minha conta?",
          text: "Todos os seus dados relacionados serão removidos permanentemente.",
          confirmButtonText: "Sim, excluir conta",
          cancelButtonText: "Voltar",
        }).then(function (result) {
          if (!result.isConfirmed) return;
          request({ action: "excluir" }).then(function (response) {
            if (!response.success) {
              window.SwalTP.erro("Erro", response.message || "Não foi possível excluir a conta.");
              return;
            }
            window.SwalTP.fire({
              icon: "success", title: "Conta excluída", text: "Você será redirecionado.",
              timer: 1800, showConfirmButton: false,
            }).then(function () { window.location.href = "../index.php"; });
          }).catch(function () {
            window.SwalTP.erro("Erro", "Não foi possível excluir a conta.");
          });
        });
      });
    }

    // ── Alterar senha ──
    formSenha.addEventListener("submit", function (event) {
      event.preventDefault();
      var novaSenha = document.getElementById("perfil-nova-senha").value;
      var confirmacao = document.getElementById("perfil-confirmar").value;
      if (novaSenha !== confirmacao) {
        window.SwalTP.aviso("Atenção", "As senhas não coincidem.");
        return;
      }
      request({
        action: "senha",
        senha_atual: document.getElementById("perfil-senha-atual").value,
        nova_senha: novaSenha,
      }).then(function (response) {
        if (response.success) {
          window.SwalTP.sucesso("Senha alterada!");
          formSenha.reset();
          return;
        }
        window.SwalTP.erro("Erro", response.message || "Não foi possível alterar.");
      }).catch(function () {
        window.SwalTP.erro("Erro", "Não foi possível alterar a senha.");
      });
    });
  });
})();

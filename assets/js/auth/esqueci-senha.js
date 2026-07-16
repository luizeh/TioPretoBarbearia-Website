// esqueci-senha.js — recuperação de senha em 3 passos (solicitar → validar → redefinir)
(function () {
  "use strict";

  var API = "../api/auth/recuperar-senha.php";
  var csrf = (document.getElementById("csrf_token") || {}).value || "";

  var formSolicitar = document.getElementById("form-solicitar");
  var formCodigo = document.getElementById("form-codigo");
  var formNovaSenha = document.getElementById("form-nova-senha");
  if (!formSolicitar) return;

  var identInput = document.getElementById("identificador");
  var identLabel = document.getElementById("ident-label");
  var codigoInput = document.getElementById("codigo");
  var btnReenviar = document.getElementById("btn-reenviar");
  var statusEl = document.getElementById("reenvio-status");
  var codigoIntro = document.getElementById("codigo-intro");
  var timer = null;

  function req(payload) {
    return fetch(API, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": csrf },
      body: JSON.stringify(payload),
    }).then(function (r) {
      return r.json().catch(function () { return { success: false, message: "Resposta inválida do servidor." }; });
    });
  }

  function erroCampo(campo, msg) {
    var input = document.getElementById(campo);
    if (input) input.classList.add("is-invalid");
    var el = document.querySelector('[data-error-for="' + campo + '"]');
    if (el) { el.textContent = msg; el.hidden = false; }
  }
  function limparErro(campo) {
    var input = document.getElementById(campo);
    if (input) input.classList.remove("is-invalid");
    var el = document.querySelector('[data-error-for="' + campo + '"]');
    if (el) { el.textContent = ""; el.hidden = true; }
  }

  function mostrarPasso(step) {
    document.querySelectorAll(".recover-step").forEach(function (f) {
      f.hidden = f.getAttribute("data-step") !== step;
    });
  }

  function iniciarCooldown(segundos) {
    if (timer) clearInterval(timer);
    var restante = segundos;
    btnReenviar.disabled = true;
    function tick() {
      if (restante <= 0) {
        clearInterval(timer);
        timer = null;
        btnReenviar.disabled = false;
        if (statusEl) statusEl.textContent = "";
        return;
      }
      if (statusEl) statusEl.textContent = "Aguarde " + restante + "s para reenviar.";
      restante--;
    }
    tick();
    timer = setInterval(tick, 1000);
  }

  // Alterna o rótulo/placeholder/tipo conforme o canal escolhido.
  document.querySelectorAll('input[name="canal"]').forEach(function (radio) {
    radio.addEventListener("change", function () {
      limparErro("identificador");
      if (radio.value === "whatsapp") {
        identLabel.textContent = "Telefone cadastrado";
        identInput.type = "tel";
        identInput.placeholder = "(00) 0 0000-0000";
        identInput.value = "";
      } else {
        identLabel.textContent = "E-mail cadastrado";
        identInput.type = "text";
        identInput.placeholder = "seu@email.com";
        identInput.value = "";
      }
    });
  });

  [identInput, codigoInput].forEach(function (el) {
    if (el) el.addEventListener("input", function () { limparErro(el.id); });
  });
  if (codigoInput) {
    codigoInput.addEventListener("input", function () {
      codigoInput.value = codigoInput.value.replace(/\D/g, "").slice(0, 6);
    });
  }

  // ── Passo 1: solicitar ──
  formSolicitar.addEventListener("submit", function (e) {
    e.preventDefault();
    limparErro("identificador");
    var canal = (document.querySelector('input[name="canal"]:checked') || {}).value || "email";
    var ident = identInput.value.trim();
    if (!ident) {
      erroCampo("identificador", "Informe seu " + (canal === "whatsapp" ? "telefone." : "e-mail."));
      return;
    }
    var btn = document.getElementById("btn-solicitar");
    btn.disabled = true;
    req({ action: "solicitar", canal: canal, identificador: ident }).then(function (resposta) {
      btn.disabled = false;
      // Resposta sempre genérica — seguimos para o passo do código de qualquer forma.
      if (codigoIntro) {
        codigoIntro.textContent =
          "Se houver uma conta com esses dados, enviamos um código de 6 dígitos" +
          (canal === "whatsapp" ? " pelo WhatsApp." : " para o e-mail.");
      }
      SwalTP.fire({
        icon: "info",
        title: "Verifique suas mensagens",
        text: resposta.message,
        confirmButtonText: "Já tenho o código",
        showCloseButton: false,
      }).then(function () {
        mostrarPasso("codigo");
        iniciarCooldown(60);
        codigoInput.focus();
      });
    }).catch(function () {
      btn.disabled = false;
      SwalTP.erro("Erro de conexão", "Não foi possível conectar ao servidor.");
    });
  });

  // ── Passo 2: validar código ──
  formCodigo.addEventListener("submit", function (e) {
    e.preventDefault();
    limparErro("codigo");
    var codigo = codigoInput.value.trim();
    if (!/^\d{6}$/.test(codigo)) {
      erroCampo("codigo", "Informe o código de 6 dígitos.");
      return;
    }
    var btn = document.getElementById("btn-validar");
    btn.disabled = true;
    req({ action: "validar", codigo: codigo }).then(function (resposta) {
      btn.disabled = false;
      if (!resposta.success) {
        erroCampo("codigo", resposta.message || "Código incorreto.");
        SwalTP.erro("Código inválido", resposta.message);
        return;
      }
      mostrarPasso("senha");
      document.getElementById("nova_senha").focus();
    }).catch(function () {
      btn.disabled = false;
      SwalTP.erro("Erro de conexão", "Não foi possível conectar ao servidor.");
    });
  });

  // ── Reenviar código ──
  btnReenviar.addEventListener("click", function () {
    if (btnReenviar.disabled) return;
    btnReenviar.disabled = true;
    req({ action: "reenviar" }).then(function (resposta) {
      if (!resposta.success) {
        var espera = resposta.data && resposta.data.espera ? resposta.data.espera : 0;
        if (espera > 0) iniciarCooldown(espera); else btnReenviar.disabled = false;
        SwalTP.aviso("Aguarde um instante", resposta.message);
        return;
      }
      SwalTP.sucesso("Código reenviado!", resposta.message);
      iniciarCooldown((resposta.data && resposta.data.espera) || 60);
    }).catch(function () {
      btnReenviar.disabled = false;
      SwalTP.erro("Erro de conexão", "Não foi possível reenviar o código.");
    });
  });

  // ── Passo 3: redefinir senha ──
  formNovaSenha.addEventListener("submit", function (e) {
    e.preventDefault();
    limparErro("nova_senha");
    limparErro("confirmar_senha");
    var nova = document.getElementById("nova_senha").value;
    var confirmar = document.getElementById("confirmar_senha").value;
    if (nova.length < 8) {
      erroCampo("nova_senha", "A senha deve ter no mínimo 8 caracteres.");
      return;
    }
    if (/\s/.test(nova)) {
      erroCampo("nova_senha", "A senha não pode conter espaços.");
      return;
    }
    if (nova !== confirmar) {
      erroCampo("confirmar_senha", "As senhas não coincidem.");
      return;
    }
    var btn = document.getElementById("btn-redefinir");
    btn.disabled = true;
    req({ action: "redefinir", nova_senha: nova, confirmar_senha: confirmar }).then(function (resposta) {
      if (!resposta.success) {
        btn.disabled = false;
        SwalTP.erro("Não foi possível redefinir", resposta.message);
        return;
      }
      SwalTP.fire({
        icon: "success",
        title: "Senha redefinida!",
        text: resposta.message,
        confirmButtonText: "Ir para o login",
        showCloseButton: false,
      }).then(function () {
        window.location.href = (resposta.data && resposta.data.redirect) || "login.php";
      });
    }).catch(function () {
      btn.disabled = false;
      SwalTP.erro("Erro de conexão", "Não foi possível conectar ao servidor.");
    });
  });
})();

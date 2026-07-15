// verificar-email.js — confirmação de e-mail por código (com reenvio e cooldown)
(function () {
  "use strict";

  var API = "../api/auth/verificar-email.php";
  var form = document.getElementById("form-verificar");
  if (!form) return;

  var input = document.getElementById("codigo");
  var btnVerificar = document.getElementById("btn-verificar");
  var btnReenviar = document.getElementById("btn-reenviar");
  var statusEl = document.getElementById("reenvio-status");
  var csrf = (form.querySelector('[name="csrf_token"]') || {}).value || "";
  var timer = null;

  function erroCampo(msg) {
    var el = form.querySelector('[data-error-for="codigo"]');
    input.classList.add("is-invalid");
    if (el) { el.textContent = msg; el.hidden = false; }
  }
  function limparErro() {
    var el = form.querySelector('[data-error-for="codigo"]');
    input.classList.remove("is-invalid");
    if (el) { el.textContent = ""; el.hidden = true; }
  }

  // Só dígitos no campo de código.
  input.addEventListener("input", function () {
    input.value = input.value.replace(/\D/g, "").slice(0, 6);
    limparErro();
  });

  function req(payload) {
    return fetch(API, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": csrf },
      body: JSON.stringify(payload),
    }).then(function (r) {
      return r.json().catch(function () { return { success: false, message: "Resposta inválida do servidor." }; });
    });
  }

  // Contagem regressiva do reenvio.
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

  // Ao abrir a página, respeita um cooldown eventualmente em curso.
  fetch(API, { method: "GET" })
    .then(function (r) { return r.json(); })
    .then(function (resposta) {
      if (resposta && resposta.success && resposta.data && resposta.data.espera > 0) {
        iniciarCooldown(resposta.data.espera);
      }
    })
    .catch(function () {});

  // Verificar código.
  form.addEventListener("submit", function (event) {
    event.preventDefault();
    limparErro();
    var codigo = input.value.trim();
    if (!/^\d{6}$/.test(codigo)) {
      erroCampo("Informe o código de 6 dígitos.");
      input.focus();
      return;
    }
    btnVerificar.disabled = true;
    req({ action: "verificar", codigo: codigo }).then(function (resposta) {
      if (!resposta.success) {
        btnVerificar.disabled = false;
        erroCampo(resposta.message || "Código incorreto.");
        SwalTP.erro("Não foi possível verificar", resposta.message);
        return;
      }
      SwalTP.fire({
        icon: "success",
        title: "E-mail verificado!",
        text: resposta.message,
        confirmButtonText: "Ir para o login",
        showCloseButton: false,
      }).then(function () {
        var redirect = resposta.data && resposta.data.redirect ? resposta.data.redirect : "login.php";
        window.location.href = redirect;
      });
    }).catch(function () {
      btnVerificar.disabled = false;
      SwalTP.erro("Erro de conexão", "Não foi possível conectar ao servidor.");
    });
  });

  // Reenviar código.
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
})();

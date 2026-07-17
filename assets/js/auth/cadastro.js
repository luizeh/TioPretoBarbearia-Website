// cadastro.js — Validação de front-end + fetch com SweetAlert para erros da API

(function () {
  "use strict";

  function avaliarSenha(valor) {
    var nivel = 0;
    if (valor.length >= 6) nivel++;
    if (valor.length >= 10) nivel++;
    if (/[A-Z]/.test(valor) && /[0-9]/.test(valor)) nivel++;
    if (/[^A-Za-z0-9]/.test(valor)) nivel++;

    var strengthBar = document.getElementById("strength-bar");
    if (strengthBar) strengthBar.dataset.level = String(nivel);
  }

  document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("form-cadastro");
    if (!form) return;

    var campos = {
      nome: document.getElementById("nome"),
      sobrenome: document.getElementById("sobrenome"),
      telefone: document.getElementById("telefone"),
      cidade: document.getElementById("cidade"),
      email: document.getElementById("email"),
      senha: document.getElementById("senha"),
      confirmar_senha: document.getElementById("confirmar_senha"),
    };

    var REGEX = {
      texto: /^[\p{L}\s]{2,50}$/u,
      telefone: /^\+55\s\(\d{2}\)\s\d{5}-\d{4}$/,
      email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    };

    function mostrarErro(campo, mensagem) {
      var input = campos[campo];
      if (input) input.classList.add("is-invalid");
      var alvo = form.querySelector('[data-error-for="' + campo + '"]');
      if (alvo) { alvo.textContent = mensagem; alvo.hidden = false; }
    }
    function limparErro(campo) {
      var input = campos[campo];
      if (input) input.classList.remove("is-invalid");
      var alvo = form.querySelector('[data-error-for="' + campo + '"]');
      if (alvo) { alvo.textContent = ""; alvo.hidden = true; }
    }

    // Limpa o erro ao editar o campo.
    Object.keys(campos).forEach(function (nome) {
      campos[nome].addEventListener("input", function () { limparErro(nome); });
    });

    if (campos.senha) {
      campos.senha.addEventListener("input", function () { avaliarSenha(campos.senha.value); });
    }

    // Validação de front-end. Retorna true se tudo estiver válido.
    function validar() {
      var ok = true;
      function falha(campo, msg) { mostrarErro(campo, msg); ok = false; }

      [["nome", "Informe seu nome (apenas letras)."],
       ["sobrenome", "Informe seu sobrenome (apenas letras)."],
       ["cidade", "Informe sua cidade (apenas letras)."]].forEach(function (par) {
        var v = campos[par[0]].value.trim();
        if (!v) falha(par[0], "Este campo é obrigatório.");
        else if (!REGEX.texto.test(v)) falha(par[0], par[1]);
      });

      if (!REGEX.telefone.test(campos.telefone.value.trim())) {
        falha("telefone", "Telefone inválido. Use o formato +55 (00) 00000-0000.");
      }

      if (!REGEX.email.test(campos.email.value.trim())) {
        falha("email", "Informe um e-mail válido.");
      }

      var senha = campos.senha.value;
      if (senha.length < 8) falha("senha", "A senha deve ter no mínimo 8 caracteres.");
      else if (/\s/.test(senha)) falha("senha", "A senha não pode conter espaços.");

      if (campos.confirmar_senha.value !== senha) {
        falha("confirmar_senha", "As senhas não coincidem.");
      }

      if (!ok) {
        var primeiro = form.querySelector(".is-invalid");
        if (primeiro) primeiro.focus();
      }
      return ok;
    }

    var enviando = false; // trava contra envios duplicados
    var btnSubmit = form.querySelector('button[type="submit"]') || form.querySelector(".btn-primary");

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (enviando) return;            // ignora cliques repetidos
      if (!validar()) return;

      enviando = true;
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.dataset.textoOriginal = btnSubmit.innerHTML;
        btnSubmit.innerHTML = "Criando conta...";
      }

      // Feedback visual imediato — não deixa a página parecer travada.
      SwalTP.fire({
        title: "Criando sua conta...",
        text: "Estamos preparando o código de verificação.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: function () { Swal.showLoading(); },
      });

      function liberar() {
        enviando = false;
        if (btnSubmit) {
          btnSubmit.disabled = false;
          if (btnSubmit.dataset.textoOriginal) btnSubmit.innerHTML = btnSubmit.dataset.textoOriginal;
        }
      }

      var data = new FormData(form);

      fetch(form.getAttribute("action"), { method: "POST", body: data })
        .then(function (r) { return r.text(); })
        .then(function (text) {
          var result;
          try {
            result = JSON.parse(text);
          } catch (parseErr) {
            liberar();
            SwalTP.erro("Erro", "Resposta inválida do servidor. Tente novamente.");
            return;
          }
          if (!result) { liberar(); return; }
          if (result.success) {
            // Mantém a conta protegida contra reenvio: NÃO reabilita o botão,
            // pois vamos redirecionar para a verificação.
            SwalTP.fire({
              icon: "success",
              title: "Quase lá!",
              text: result.message || "Enviamos um código para o seu e-mail.",
              confirmButtonText: "Confirmar e-mail",
              showCloseButton: false,
              allowOutsideClick: false,
            }).then(function () {
              var redirect = result.data && result.data.redirect ? result.data.redirect : "verificar-email.php";
              window.location.href = redirect;
            });
          } else {
            liberar();
            SwalTP.erro("Erro no cadastro", result.message || "Ocorreu um erro. Tente novamente.");
          }
        })
        .catch(function () {
          liberar();
          SwalTP.erro("Erro", "Não foi possível conectar ao servidor.");
        });
    });
  });
})();

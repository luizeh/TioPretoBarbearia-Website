// Operacoes da pagina de perfil do cliente.
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var base = window.API_BASE || "../../api/";
    var formPerfil = document.getElementById("form-perfil");
    var formSenha = document.getElementById("form-senha");
    var buttonExcluir = document.getElementById("btn-excluir-conta");

    if (!formPerfil || !formSenha) return;

    function request(payload) {
      return fetch(base + "user/perfil.php", {
        method: payload ? "POST" : "GET",
        headers: payload ? { "Content-Type": "application/json" } : {},
        body: payload ? JSON.stringify(payload) : undefined,
      }).then(function (response) {
        return response.json();
      });
    }

    request()
      .then(function (response) {
        if (!response.success) return;

        var user = response.data || {};
        document.getElementById("perfil-nome").value = user.nome || "";
        document.getElementById("perfil-sobrenome").value = user.sobrenome || "";
        document.getElementById("perfil-telefone").value = user.telefone || "";
        document.getElementById("perfil-cidade").value = user.cidade || "";
        document.getElementById("perfil-email").value = user.email || "";
      })
      .catch(function () {
        window.SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: "Não foi possível carregar seu perfil.",
        });
      });

    formPerfil.addEventListener("submit", function (event) {
      event.preventDefault();

      request({
        action: "editar",
        nome: document.getElementById("perfil-nome").value.trim(),
        sobrenome: document.getElementById("perfil-sobrenome").value.trim(),
        telefone: document.getElementById("perfil-telefone").value.trim(),
        cidade: document.getElementById("perfil-cidade").value.trim(),
        email: document.getElementById("perfil-email").value.trim(),
      })
        .then(function (response) {
          if (response.success) {
            window.SwalTP.fire({
              icon: "success",
              title: "Salvo!",
              text: "Perfil atualizado.",
              timer: 2000,
              showConfirmButton: false,
            });
            return;
          }

          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: response.message || "Não foi possível salvar.",
          });
        })
        .catch(function () {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível salvar o perfil.",
          });
        });
    });

    if (buttonExcluir) {
      buttonExcluir.addEventListener("click", function () {
        window.SwalTP.confirmarExclusao({
          title: "Excluir minha conta?",
          text: "Todos os seus dados relacionados serão removidos permanentemente.",
          confirmButtonText: "Sim, excluir conta",
          cancelButtonText: "Voltar",
        }).then(function (result) {
          if (!result.isConfirmed) return;

          request({ action: "excluir" })
            .then(function (response) {
              if (!response.success) {
                window.SwalTP.fire({
                  icon: "error",
                  title: "Erro",
                  text: response.message || "Não foi possível excluir a conta.",
                });
                return;
              }

              window.SwalTP.fire({
                icon: "success",
                title: "Conta excluída",
                text: "Você será redirecionado.",
                timer: 1800,
                showConfirmButton: false,
              }).then(function () {
                window.location.href = "../index.php";
              });
            })
            .catch(function () {
              window.SwalTP.fire({
                icon: "error",
                title: "Erro",
                text: "Não foi possível excluir a conta.",
              });
            });
        });
      });
    }

    formSenha.addEventListener("submit", function (event) {
      event.preventDefault();

      var novaSenha = document.getElementById("perfil-nova-senha").value;
      var confirmacao = document.getElementById("perfil-confirmar").value;
      if (novaSenha !== confirmacao) {
        window.SwalTP.fire({
          icon: "warning",
          title: "Atenção",
          text: "As senhas não coincidem.",
        });
        return;
      }

      request({
        action: "senha",
        senha_atual: document.getElementById("perfil-senha-atual").value,
        nova_senha: novaSenha,
      })
        .then(function (response) {
          if (response.success) {
            window.SwalTP.fire({
              icon: "success",
              title: "Senha alterada!",
              timer: 2000,
              showConfirmButton: false,
            });
            formSenha.reset();
            return;
          }

          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: response.message || "Não foi possível alterar.",
          });
        })
        .catch(function () {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível alterar a senha.",
          });
        });
    });
  });
})();

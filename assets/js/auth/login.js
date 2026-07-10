// login.js — Intercepta o form de login para exibir erros via SweetAlert2
document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("form-login");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var data = new FormData(form);

    fetch(form.getAttribute("action"), { method: "POST", body: data })
      .then(function (r) {
        return r.text();
      })
      .then(function (text) {
        var result;
        try {
          result = JSON.parse(text);
        } catch (parseErr) {
          SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: "Resposta inválida do servidor. Tente novamente.",
            confirmButtonText: "Ok",
          });
          return;
        }
        if (!result) return;
        if (result.success) {
          SwalTP.fire({
            icon: "success",
            title: "Bem-vindo!",
            text: "Login realizado com sucesso.",
            confirmButtonText: "Entrar",
            showCloseButton: false,
            timer: 1800,
            timerProgressBar: true,
          }).then(function () {
            var redirect =
              result.data && result.data.redirect
                ? result.data.redirect
                : "admin/dashboard.php";
            window.location.href = redirect;
          });
        } else {
          SwalTP.fire({
            icon: "error",
            title: "Acesso negado",
            text: result.message || "E-mail ou senha inválidos.",
            confirmButtonText: "Tentar novamente",
          });
        }
      })
      .catch(function () {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: "Não foi possível conectar ao servidor.",
          confirmButtonText: "Ok",
        });
      });
  });
});

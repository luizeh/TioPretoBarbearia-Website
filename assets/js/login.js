// login.js — Intercepta o form de login para exibir erros via SweetAlert2
document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("form-login");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var data = new FormData(form);

    fetch(form.action, { method: "POST", body: data })
      .then(function (r) {
        if (r.redirected) {
          window.location.href = r.url;
          return null;
        }
        return r.json();
      })
      .then(function (result) {
        if (!result) return;
        if (result.success) {
          window.location.href = result.data
            ? result.data.redirect || "../view/admin/dashboard.php"
            : "../view/admin/dashboard.php";
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

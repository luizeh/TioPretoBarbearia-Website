// cadastro.js — Validação visual + fetch com Swal para erros da API

function avaliarSenha(valor) {
  const bars = [
    document.getElementById("s1"),
    document.getElementById("s2"),
    document.getElementById("s3"),
    document.getElementById("s4"),
  ];
  const cores = ["#e74c3c", "#e67e22", "#f1c40f", "#c9963a"];

  let nivel = 0;
  if (valor.length >= 6) nivel++;
  if (valor.length >= 10) nivel++;
  if (/[A-Z]/.test(valor) && /[0-9]/.test(valor)) nivel++;
  if (/[^A-Za-z0-9]/.test(valor)) nivel++;

  bars.forEach((bar, i) => {
    bar.style.background =
      i < nivel ? cores[nivel - 1] : "rgba(255,255,255,0.1)";
  });
}

// Intercepta o form de cadastro para exibir erros da API via SweetAlert2
document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("form-cadastro");
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
            title: "Cadastro realizado!",
            text: "Sua conta foi criada com sucesso. Faça login para continuar.",
            confirmButtonText: "Ir para o login",
            showCloseButton: false,
          }).then(function () {
            var redirect =
              result.data && result.data.redirect
                ? result.data.redirect
                : "login.php";
            window.location.href = redirect;
          });
        } else {
          SwalTP.fire({
            icon: "error",
            title: "Erro no cadastro",
            text: result.message || "Ocorreu um erro. Tente novamente.",
            confirmButtonText: "Ok",
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

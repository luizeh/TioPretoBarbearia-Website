// site-editor.js — Salva o conteúdo editável do site via painel admin.
(function () {
  "use strict";

  document.querySelectorAll("[data-save-grupo]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var grupo = btn.dataset.saveGrupo;
      var campos = document.querySelectorAll(
        '[data-chave][data-grupo="' + grupo + '"]',
      );

      if (!campos.length) {
        window.SwalTP.fire({
          icon: "warning",
          title: "Nenhum campo encontrado",
          text: "Não há campos para salvar neste grupo.",
        });
        return;
      }

      var promessas = Array.prototype.map.call(campos, function (el) {
        return fetch("../../api/admin/site-config.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ chave: el.dataset.chave, valor: el.value }),
        })
          .then(function (r) {
            return r.json();
          })
          .catch(function () {
            return { success: false, message: "Erro de rede." };
          });
      });

      Promise.all(promessas).then(function (resultados) {
        var falhas = resultados.filter(function (r) {
          return !r.success;
        });

        if (!falhas.length) {
          window.SwalTP.fire({
            icon: "success",
            title: "Salvo!",
            text: "Conteúdo atualizado com sucesso.",
            timer: 2000,
            showConfirmButton: false,
          });
        } else {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro ao salvar",
            text: falhas
              .map(function (r) {
                return r.message;
              })
              .join(" | "),
          });
        }
      });
    });
  });
})();

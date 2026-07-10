// swal-modals.js — Sistema de modais via SweetAlert2
// Substitui o sistema de [data-modal] / .modal-overlay do dashboard.js
(function () {
  // IDs tratados por JS de página específica
  var SKIP = [
    "modal-produto",
    "modal-produto-ver",
    "modal-produto-excluir",
    "modal-servico",
    "modal-servico-excluir",
    "modal-agendamento",
    "modal-agendamento-excluir",
    "modal-cliente-editar",
    "modal-cliente-excluir",
    "modal-tag",
    "modal-tag-excluir",
  ];

  // Popula [data-field] dentro do popup a partir do dataset do botão
  function populateFields(popup, dataset) {
    Object.keys(dataset).forEach(function (key) {
      if (key === "modal") return;
      var el = popup.querySelector('[data-field="' + key + '"]');
      if (!el) return;
      var val = dataset[key] || "";
      if (el.tagName === "IMG") {
        el.src = val;
        el.hidden = !val;
      } else if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
        el.value = val;
      } else if (el.tagName === "SELECT") {
        el.value = val;
      } else {
        el.textContent = val || "—";
      }
    });
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-modal]");
    if (!btn) return;

    var id = btn.dataset.modal;
    if (SKIP.indexOf(id) !== -1) return;

    var overlay = document.getElementById(id);
    if (!overlay) return;

    e.preventDefault();
    e.stopPropagation();

    var titleEl = overlay.querySelector(".modal-title");
    var title = titleEl ? titleEl.innerHTML.trim() : "";
    var bodyEl = overlay.querySelector(".modal-body");
    var bodyHTML = bodyEl ? bodyEl.innerHTML : "";
    var isDelete = id.indexOf("-excluir") !== -1;
    var isView = id.indexOf("-ver") !== -1;

    if (isDelete) {
      SwalTP.fire({
        title: title,
        html: bodyHTML,
        showCancelButton: true,
        confirmButtonText: "Excluir",
        cancelButtonText: "Cancelar",
        customClass: {
          popup: "swal-tp",
          title: "swal-tp__title",
          htmlContainer: "swal-tp__body",
          confirmButton: "swal-tp__btn swal-tp__btn--danger",
          cancelButton: "swal-tp__btn swal-tp__btn--cancel",
          actions: "swal-tp__actions",
          closeButton: "swal-tp__close",
        },
        didOpen: function (popup) {
          populateFields(popup, btn.dataset);
        },
      });
    } else if (isView) {
      SwalTP.fire({
        title: title,
        html: bodyHTML,
        confirmButtonText: "Fechar",
        showCancelButton: false,
        didOpen: function (popup) {
          populateFields(popup, btn.dataset);
        },
      });
    } else {
      // Modal de formulário
      SwalTP.fire({
        title: title,
        html: bodyHTML,
        showCancelButton: true,
        confirmButtonText: "Salvar",
        cancelButtonText: "Cancelar",
        didOpen: function (popup) {
          populateFields(popup, btn.dataset);
          // Reaplicar máscara de telefone se existir
          if (typeof window.aplicarMascaraTelefone === "function") {
            popup
              .querySelectorAll('[type="tel"]')
              .forEach(window.aplicarMascaraTelefone);
          }
        },
      });
    }
  });
})();

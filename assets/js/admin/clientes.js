// clientes.js — CRUD de clientes via SweetAlert2 + API

(function () {
  // ─── Criar Cliente ──────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-cliente-criar"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-cliente-criar");
    if (!overlay) return;

    var bodyHTML = overlay.querySelector(".modal-body").innerHTML;

    SwalTP.fire({
      title: "Novo Cliente",
      html: bodyHTML,
      showCancelButton: true,
      confirmButtonText: "Cadastrar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        if (typeof window.aplicarMascaraTelefone === "function") {
          popup
            .querySelectorAll('[type="tel"]')
            .forEach(window.aplicarMascaraTelefone);
        }
        // Toggle de senha padrão / personalizada
        var senhaCheckbox = popup.querySelector('[data-field="senha-padrao"]');
        var senhaInput = popup.querySelector('[data-field="senha"]');
        if (senhaCheckbox && senhaInput) {
          senhaCheckbox.addEventListener("change", function () {
            senhaInput.style.display = this.checked ? "none" : "";
            if (!this.checked) senhaInput.focus();
          });
        }
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var senhaCheckbox = popup.querySelector('[data-field="senha-padrao"]');
        var senhaInput = popup.querySelector('[data-field="senha"]');
        var dados = {
          action: "criar",
          nome: popup.querySelector('[data-field="nome"]').value.trim(),
          sobrenome: popup
            .querySelector('[data-field="sobrenome"]')
            .value.trim(),
          email: popup.querySelector('[data-field="email"]').value.trim(),
          telefone: popup.querySelector('[data-field="telefone"]').value.trim(),
          cidade: popup.querySelector('[data-field="cidade"]').value.trim(),
          senha: "12345678",
        };
        if (senhaCheckbox && !senhaCheckbox.checked) {
          var senhaPers = senhaInput ? senhaInput.value.trim() : "";
          if (!senhaPers) {
            Swal.showValidationMessage(
              "Digite a senha personalizada ou marque \u2018Usar senha padrão\u2019.",
            );
            return false;
          }
          dados.senha = senhaPers;
        }
        if (!dados.nome || !dados.email || !dados.telefone) {
          Swal.showValidationMessage(
            "Nome, e-mail e telefone são obrigatórios.",
          );
          return false;
        }
        return fetch("../../api/admin/clientes.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(dados),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Cliente cadastrado!",
          text: "O cliente foi adicionado com sucesso.",
          timer: 1800,
          timerProgressBar: true,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao cadastrar.",
        });
      }
    });
  });

  // ─── Editar Cliente ──────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-cliente-editar"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-cliente-editar");
    if (!overlay) return;

    var bodyHTML = overlay.querySelector(".modal-body").innerHTML;

    SwalTP.fire({
      title: "Editar Cliente",
      html: bodyHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        // Aplicar máscara antes de preencher para que o disparo de 'input' formate o valor
        if (typeof window.aplicarMascaraTelefone === "function") {
          popup
            .querySelectorAll('[type="tel"]')
            .forEach(window.aplicarMascaraTelefone);
        }
        var fields = ["nome", "sobrenome", "email", "cidade"];
        fields.forEach(function (key) {
          var el = popup.querySelector('[data-field="' + key + '"]');
          if (el) el.value = btn.dataset[key] || "";
        });
        // Preenche e formata o telefone
        var telEl = popup.querySelector('[data-field="telefone"]');
        if (telEl) {
          telEl.value = btn.dataset.telefone || "";
          telEl.dispatchEvent(new Event("input"));
        }
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var dados = {
          action: "editar",
          id: btn.dataset.id,
          nome: popup.querySelector('[data-field="nome"]').value.trim(),
          sobrenome: popup
            .querySelector('[data-field="sobrenome"]')
            .value.trim(),
          email: popup.querySelector('[data-field="email"]').value.trim(),
          telefone: popup.querySelector('[data-field="telefone"]').value.trim(),
          cidade: popup.querySelector('[data-field="cidade"]').value.trim(),
        };
        if (!dados.nome || !dados.email) {
          Swal.showValidationMessage("Nome e e-mail são obrigatórios.");
          return false;
        }
        return fetch("../../api/admin/clientes.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(dados),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Salvo!",
          text: "Cliente atualizado com sucesso.",
          timer: 1800,
          timerProgressBar: true,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao salvar.",
        });
      }
    });
  });

  // ─── Excluir Cliente ─────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-cliente-excluir"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-cliente-excluir");
    if (!overlay) return;

    var bodyHTML = overlay.querySelector(".modal-body").innerHTML;

    SwalTP.confirmarExclusao({
      title: "Excluir Cliente",
      html: bodyHTML,
      confirmButtonText: "Excluir",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        var el = popup.querySelector("[data-field='nome']");
        if (el) el.textContent = btn.dataset.nome || "este cliente";
      },
      preConfirm: function () {
        return fetch("../../api/admin/clientes.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "excluir", id: btn.dataset.id }),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Excluído!",
          text: "Cliente removido.",
          timer: 1500,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao excluir.",
        });
      }
    });
  });
})();

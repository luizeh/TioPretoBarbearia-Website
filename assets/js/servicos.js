// servicos.js — CRUD de serviços via SweetAlert2 + API

(function () {
  // ─── Helper: coleta campos do popup ─────────────────────────────
  function coletarDados(popup) {
    return {
      nome: (popup.querySelector('[data-field="nome"]') || {}).value || "",
      duracao:
        (popup.querySelector('[data-field="duracao"]') || {}).value || "",
      preco: (popup.querySelector('[data-field="preco"]') || {}).value || "",
      descricao:
        (popup.querySelector('[data-field="descricao"]') || {}).value || "",
    };
  }

  // ─── Criar / Editar Serviço ──────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-servico"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-servico");
    if (!overlay) return;

    var isEdit = !!btn.dataset.id;
    var bodyHTML = overlay.querySelector(".modal-body").innerHTML;

    SwalTP.fire({
      title: isEdit ? "Editar Serviço" : "Novo Serviço",
      html: bodyHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        if (isEdit) {
          ["nome", "duracao", "preco", "descricao"].forEach(function (key) {
            var el = popup.querySelector('[data-field="' + key + '"]');
            if (el) el.value = btn.dataset[key] || "";
          });
        }
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var dados = coletarDados(popup);
        if (!dados.nome || !dados.preco || !dados.duracao) {
          Swal.showValidationMessage("Nome, preço e duração são obrigatórios.");
          return false;
        }
        var payload = {
          action: isEdit ? "editar" : "criar",
          nome: dados.nome,
          preco: dados.preco,
          tempo_estimado: dados.duracao,
          descricao: dados.descricao,
        };
        if (isEdit) payload.id = btn.dataset.id;

        return fetch("../../api/admin/servicos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: isEdit ? "Atualizado!" : "Criado!",
          text: isEdit ? "Serviço atualizado." : "Serviço adicionado.",
          timer: 1800,
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

  // ─── Excluir Serviço ─────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-servico-excluir"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-servico-excluir");
    if (!overlay) return;

    SwalTP.fire({
      title: "Excluir Serviço",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Excluir",
      cancelButtonText: "Cancelar",
      customClass: { confirmButton: "swal-tp__btn swal-tp__btn--danger" },
      didOpen: function (popup) {
        var el = popup.querySelector("[data-field='nome']");
        if (el) el.textContent = btn.dataset.nome || "este serviço";
      },
      preConfirm: function () {
        return fetch("../../api/admin/servicos.php", {
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

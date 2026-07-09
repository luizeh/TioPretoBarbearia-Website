// tags.js — CRUD de tags via SweetAlert2 + API

(function () {
  // ─── Criar / Editar Tag ──────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-tag"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-tag");
    if (!overlay) return;

    var isEdit = !!btn.dataset.id;
    SwalTP.fire({
      title: isEdit ? "Editar Tag" : "Nova Tag",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        if (isEdit) {
          var el = popup.querySelector('[data-field="nome"]');
          if (el) el.value = btn.dataset.nome || "";
        }
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var nome = (
          (popup.querySelector('[data-field="nome"]') || {}).value || ""
        ).trim();
        if (!nome) {
          Swal.showValidationMessage("O nome da tag é obrigatório.");
          return false;
        }
        var payload = { action: isEdit ? "editar" : "criar", nome: nome };
        if (isEdit) payload.id = btn.dataset.id;
        return fetch("../../api/admin/tags.php", {
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
          title: isEdit ? "Atualizada!" : "Criada!",
          timer: 1500,
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

  // ─── Excluir Tag ─────────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-tag-excluir"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-tag-excluir");
    if (!overlay) return;

    SwalTP.fire({
      title: "Excluir Tag",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Excluir",
      cancelButtonText: "Cancelar",
      customClass: { confirmButton: "swal-tp__btn swal-tp__btn--danger" },
      didOpen: function (popup) {
        var el = popup.querySelector("[data-field='nome']");
        if (el) el.textContent = btn.dataset.nome || "esta tag";
      },
      preConfirm: function () {
        return fetch("../../api/admin/tags.php", {
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
          title: "Excluída!",
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

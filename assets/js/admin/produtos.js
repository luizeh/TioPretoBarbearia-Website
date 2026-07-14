// CRUD da página de produtos do painel administrativo.
(function () {
  "use strict";

  function escapeHtml(value) {
    var element = document.createElement("div");
    element.textContent = value || "";
    return element.innerHTML;
  }

  function initTagPicker(context) {
    var picker = context.querySelector("#tag-picker");
    var hiddenInput = context.querySelector("#tag-hidden");
    if (!picker || !hiddenInput) return;

    picker.addEventListener("click", function (event) {
      var button = event.target.closest(".tag-option");
      if (!button) return;

      button.classList.toggle("selected");
      hiddenInput.value = Array.from(picker.querySelectorAll(".tag-option.selected"))
        .map(function (item) {
          return item.dataset.tagId;
        })
        .join(",");
    });

    (hiddenInput.value || "")
      .split(",")
      .map(function (id) {
        return id.trim();
      })
      .filter(Boolean)
      .forEach(function (id) {
        var option = picker.querySelector('[data-tag-id="' + id + '"]');
        if (option) option.classList.add("selected");
      });
  }

  function post(payload) {
    return fetch("../../api/admin/produtos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    }).then(function (response) {
      return response.json();
    });
  }

  function showSaveResult(result, isEdit) {
    if (result && result.success) {
      window.SwalTP.fire({
        icon: "success",
        title: isEdit ? "Atualizado!" : "Criado!",
        timer: 1800,
        showConfirmButton: false,
      }).then(function () {
        window.location.reload();
      });
      return;
    }

    window.SwalTP.fire({
      icon: "error",
      title: "Erro",
      text: (result && result.message) || "Erro ao salvar.",
    });
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest('[data-modal="modal-produto"]');
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    var overlay = document.getElementById("modal-produto");
    if (!overlay) return;

    var isEdit = Boolean(button.dataset.id);
    window.SwalTP.fire({
      title: isEdit ? "Editar Produto" : "Novo Produto",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      width: "640px",
      didOpen: function (popup) {
        Object.keys(button.dataset).forEach(function (key) {
          if (key === "modal") return;

          var field = popup.querySelector('[data-field="' + key + '"]');
          if (!field) return;
          field.value = button.dataset[key] || "";
        });

        if (window.AdminImageUpload) window.AdminImageUpload.init(popup);
        initTagPicker(popup);
      },
      preConfirm: function () {
        var popup = window.Swal.getPopup();
        var nome = (popup.querySelector('[data-field="nome"]') || {}).value || "";
        if (!nome.trim()) {
          window.Swal.showValidationMessage("O nome do produto é obrigatório.");
          return false;
        }

        var payload = {
          action: isEdit ? "editar" : "criar",
          nome: nome.trim(),
          descricao: ((popup.querySelector('[data-field="descricao"]') || {}).value || "").trim(),
          preco: (popup.querySelector('[data-field="preco"]') || {}).value || "0",
          estoque: (popup.querySelector('[data-field="estoque"]') || {}).value || "0",
          foto_url: (popup.querySelector('[data-field="fotoUrl"]') || {}).value || "",
          tags: (popup.querySelector('[data-field="tagIds"]') || {}).value || "",
        };

        if (isEdit) payload.id = button.dataset.id;
        return post(payload);
      },
    }).then(function (result) {
      if (result.isConfirmed) showSaveResult(result.value, isEdit);
    });
  });

  // ── Toggle de visibilidade (visível no site / só admin) ──
  document.addEventListener("click", function (event) {
    var button = event.target.closest(".btn-action--visibilidade");
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    var novoVisivel = button.dataset.visivel === "1" ? 0 : 1;
    button.disabled = true;

    post({ action: "visibilidade", id: button.dataset.id, visivel: novoVisivel }).then(function (result) {
      button.disabled = false;
      if (!result || !result.success) {
        window.SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result && result.message) || "Não foi possível alterar a visibilidade.",
        });
        return;
      }

      button.dataset.visivel = String(novoVisivel);
      var icon = button.querySelector("i");
      if (icon) icon.className = "fa-solid " + (novoVisivel ? "fa-eye" : "fa-eye-slash");
      button.title = novoVisivel
        ? "Visível no site — clique para ocultar (só admin)"
        : "Oculto (só admin) — clique para exibir no site";

      var row = button.closest("tr");
      if (row) row.classList.toggle("produto-oculto", !novoVisivel);

      window.SwalTP.fire({
        icon: "success",
        title: novoVisivel ? "Produto visível no site" : "Produto oculto (só admin)",
        timer: 1400,
        showConfirmButton: false,
      });
    });
  });

  document.addEventListener("click", function (event) {
    var button = event.target.closest('[data-modal="modal-produto-ver"]');
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    var overlay = document.getElementById("modal-produto-ver");
    if (!overlay) return;

    var photoUrl = button.dataset.fotoUrl || "";
    window.SwalTP.fire({
      title: "Detalhes do Produto",
      html: overlay.querySelector(".modal-body").innerHTML,
      confirmButtonText: "Fechar",
      showCancelButton: false,
      didOpen: function (popup) {
        Object.keys(button.dataset).forEach(function (key) {
          if (key === "modal") return;

          var field = popup.querySelector('[data-field="' + key + '"]');
          if (field) field.textContent = button.dataset[key] || "—";
        });

        var image = popup.querySelector("#ver-foto-img");
        var emptyImage = popup.querySelector("#ver-foto-vazio");
        if (image) {
          image.src = photoUrl;
          image.hidden = !photoUrl;
        }
        if (emptyImage) emptyImage.hidden = Boolean(photoUrl);
      },
    });
  });

  document.addEventListener("click", function (event) {
    var button = event.target.closest('[data-modal="modal-produto-excluir"]');
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    window.SwalTP.fire({
      title: "Excluir produto?",
      html:
        "Tem certeza que deseja excluir <strong>" +
        escapeHtml(button.dataset.nome || "este produto") +
        '</strong>?<br><small class="swal-text-muted">Esta ação não pode ser desfeita.</small>',
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
      preConfirm: function () {
        return post({ action: "excluir", id: button.dataset.id });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;

      if (result.value && result.value.success) {
        window.SwalTP.fire({
          icon: "success",
          title: "Excluído!",
          timer: 1500,
          showConfirmButton: false,
        }).then(function () {
          window.location.reload();
        });
        return;
      }

      window.SwalTP.fire({
        icon: "error",
        title: "Erro",
        text: (result.value && result.value.message) || "Erro ao excluir.",
      });
    });
  });
})();

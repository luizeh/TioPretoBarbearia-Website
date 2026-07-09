// produtos.js — Interações da página de produtos (admin) via SweetAlert2

// ─────────────────────────────────────────────────────────────────
// Dropzone de foto — inicializa dentro de um contexto (ctx = popup)
// ─────────────────────────────────────────────────────────────────
function initDropzone(ctx) {
  var dropzone = ctx.querySelector("#foto-dropzone");
  var fileInput = ctx.querySelector("#foto-file-input");
  var urlHidden = ctx.querySelector("#foto-url-hidden");
  var preview = ctx.querySelector("#foto-preview");
  var placeholder = ctx.querySelector("#foto-placeholder");
  var removeBtn = ctx.querySelector("#foto-remove");
  if (!dropzone) return;

  dropzone.addEventListener("click", function (e) {
    if (removeBtn && removeBtn.contains(e.target)) return;
    fileInput.click();
  });
  dropzone.addEventListener("dragover", function (e) {
    e.preventDefault();
    dropzone.classList.add("dragover");
  });
  dropzone.addEventListener("dragleave", function () {
    dropzone.classList.remove("dragover");
  });
  dropzone.addEventListener("drop", function (e) {
    e.preventDefault();
    dropzone.classList.remove("dragover");
    if (e.dataTransfer.files.length)
      _uploadFoto(
        e.dataTransfer.files[0],
        urlHidden,
        preview,
        placeholder,
        removeBtn,
      );
  });
  fileInput.addEventListener("change", function () {
    if (this.files.length)
      _uploadFoto(this.files[0], urlHidden, preview, placeholder, removeBtn);
  });
  if (removeBtn) {
    removeBtn.addEventListener("click", function () {
      urlHidden.value = "";
      fileInput.value = "";
      _showPreview("", preview, placeholder, removeBtn);
    });
  }
  _showPreview(urlHidden.value || "", preview, placeholder, removeBtn);
}

function _showPreview(url, preview, placeholder, removeBtn) {
  if (url) {
    preview.src = "../../" + url;
    preview.style.display = "block";
    placeholder.style.display = "none";
    if (removeBtn) removeBtn.style.display = "block";
  } else {
    preview.src = "";
    preview.style.display = "none";
    placeholder.style.display = "flex";
    if (removeBtn) removeBtn.style.display = "none";
  }
}

function _uploadFoto(file, urlHidden, preview, placeholder, removeBtn) {
  placeholder.innerHTML =
    '<i class="fa-solid fa-spinner fa-spin"></i><span>Enviando...</span>';
  placeholder.style.display = "flex";
  preview.style.display = "none";
  if (removeBtn) removeBtn.style.display = "none";
  var fd = new FormData();
  fd.append("foto", file);
  fetch("../../api/produtos/upload-foto.php", { method: "POST", body: fd })
    .then(function (r) {
      return r.json();
    })
    .then(function (d) {
      if (d.success) {
        urlHidden.value = d.url;
        _showPreview(d.url, preview, placeholder, removeBtn);
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: d.message || "Erro ao enviar.",
        });
        _resetPlaceholder(placeholder);
      }
    })
    .catch(function () {
      SwalTP.fire({ icon: "error", title: "Erro", text: "Erro de rede." });
      _resetPlaceholder(placeholder);
    });
}

function _resetPlaceholder(placeholder) {
  placeholder.innerHTML =
    '<i class="fa-solid fa-cloud-arrow-up"></i><span>Arraste ou clique para enviar</span><small>JPG, PNG, WebP · máx. 2MB</small>';
  placeholder.style.display = "flex";
}

// ─────────────────────────────────────────────────────────────────
// Tag Picker — inicializa dentro de um contexto (ctx = popup)
// ─────────────────────────────────────────────────────────────────
function initTagPicker(ctx) {
  var picker = ctx.querySelector("#tag-picker");
  var hidden = ctx.querySelector("#tag-hidden");
  if (!picker) return;
  picker.addEventListener("click", function (e) {
    var btn = e.target.closest(".tag-option");
    if (!btn) return;
    btn.classList.toggle("selected");
    hidden.value = Array.from(picker.querySelectorAll(".tag-option.selected"))
      .map(function (b) {
        return b.dataset.tagId;
      })
      .join(",");
  });
  var csv = hidden.value || "";
  if (csv) {
    var ids = csv.split(",").map(function (id) {
      return id.trim();
    });
    picker.querySelectorAll(".tag-option").forEach(function (btn) {
      if (ids.indexOf(btn.dataset.tagId) !== -1) btn.classList.add("selected");
    });
  }
}

// ─────────────────────────────────────────────────────────────────
// Modal: Novo / Editar Produto
// ─────────────────────────────────────────────────────────────────
document.addEventListener("click", function (e) {
  var btn = e.target.closest('[data-modal="modal-produto"]');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  var overlay = document.getElementById("modal-produto");
  if (!overlay) return;
  var isEdit = !!btn.dataset.id;
  var bodyHTML = overlay.querySelector(".modal-body").innerHTML;
  SwalTP.fire({
    title: isEdit ? "Editar Produto" : "Novo Produto",
    html: bodyHTML,
    showCancelButton: true,
    confirmButtonText: "Salvar",
    cancelButtonText: "Cancelar",
    width: "640px",
    didOpen: function (popup) {
      Object.keys(btn.dataset).forEach(function (key) {
        if (key === "modal") return;
        var el = popup.querySelector('[data-field="' + key + '"]');
        if (!el) return;
        if (el.tagName === "INPUT" || el.tagName === "TEXTAREA")
          el.value = btn.dataset[key] || "";
        else if (el.tagName === "SELECT") el.value = btn.dataset[key] || "";
      });
      initDropzone(popup);
      initTagPicker(popup);
    },
    preConfirm: function () {
      var popup = document.querySelector(".swal2-popup");
      var nome = (popup.querySelector('[data-field="nome"]') || {}).value || "";
      if (!nome.trim()) {
        Swal.showValidationMessage("O nome do produto é obrigatório.");
        return false;
      }
      var payload = {
        action: isEdit ? "editar" : "criar",
        nome: nome.trim(),
        descricao: (
          (popup.querySelector('[data-field="descricao"]') || {}).value || ""
        ).trim(),
        preco: (popup.querySelector('[data-field="preco"]') || {}).value || "0",
        estoque:
          (popup.querySelector('[data-field="estoque"]') || {}).value || "0",
        foto_url:
          (popup.querySelector('[data-field="fotoUrl"]') || {}).value || "",
        tags: (popup.querySelector('[data-field="tagIds"]') || {}).value || "",
      };
      if (isEdit) payload.id = btn.dataset.id;
      return fetch("../../api/admin/produtos.php", {
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

// ─────────────────────────────────────────────────────────────────
// Modal: Ver Produto
// ─────────────────────────────────────────────────────────────────
document.addEventListener("click", function (e) {
  var btn = e.target.closest('[data-modal="modal-produto-ver"]');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  var overlay = document.getElementById("modal-produto-ver");
  if (!overlay) return;
  var bodyHTML = overlay.querySelector(".modal-body").innerHTML;
  var url = btn.dataset.fotoUrl || "";
  SwalTP.fire({
    title: "Detalhes do Produto",
    html: bodyHTML,
    confirmButtonText: "Fechar",
    showCancelButton: false,
    didOpen: function (popup) {
      Object.keys(btn.dataset).forEach(function (key) {
        if (key === "modal") return;
        var el = popup.querySelector('[data-field="' + key + '"]');
        if (!el) return;
        el.textContent = btn.dataset[key] || "—";
      });
      var img = popup.querySelector("#ver-foto-img");
      var vazio = popup.querySelector("#ver-foto-vazio");
      if (img) {
        img.src = url;
        img.style.display = url ? "block" : "none";
      }
      if (vazio) {
        vazio.style.display = url ? "none" : "inline";
      }
    },
  });
});

// ─────────────────────────────────────────────────────────────────
// Modal: Excluir Produto
// ─────────────────────────────────────────────────────────────────
document.addEventListener("click", function (e) {
  var btn = e.target.closest('[data-modal="modal-produto-excluir"]');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  SwalTP.fire({
    title: "Excluir produto?",
    html:
      "Tem certeza que deseja excluir <strong>" +
      (btn.dataset.nome || "este produto") +
      "</strong>?<br/><small style='color:#888'>Esta ação não pode ser desfeita.</small>",
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
      return fetch("../../api/admin/produtos.php", {
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

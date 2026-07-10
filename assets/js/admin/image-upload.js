// Upload e preview compartilhados pelos modais de produtos e serviços.
(function () {
  "use strict";

  function notifyError(message) {
    if (window.SwalTP) {
      window.SwalTP.fire({ icon: "error", title: "Erro no upload", text: message });
    }
  }

  function init(context) {
    var dropzone = context.querySelector("#foto-dropzone");
    var fileInput = context.querySelector("#foto-file-input");
    var hiddenInput = context.querySelector("#foto-url-hidden");
    var preview = context.querySelector("#foto-preview");
    var placeholder = context.querySelector("#foto-placeholder");
    var removeButton = context.querySelector("#foto-remove");

    if (!dropzone || !fileInput || !hiddenInput || !preview || !placeholder) return;

    var defaultPlaceholder = placeholder.innerHTML;

    function setPreview(url) {
      var hasImage = Boolean(url);
      preview.src = hasImage ? "../../" + url : "";
      preview.classList.toggle("is-visible", hasImage);
      placeholder.classList.toggle("is-hidden", hasImage);
      if (removeButton) removeButton.classList.toggle("is-visible", hasImage);
    }

    function resetPlaceholder() {
      placeholder.innerHTML = defaultPlaceholder;
      placeholder.classList.remove("is-hidden");
    }

    function upload(file) {
      if (!file) return;

      placeholder.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Enviando...</span>';
      placeholder.classList.remove("is-hidden");
      preview.classList.remove("is-visible");
      if (removeButton) removeButton.classList.remove("is-visible");

      var formData = new FormData();
      formData.append("foto", file);

      fetch("../../api/produtos/upload-foto.php", { method: "POST", body: formData })
        .then(function (response) {
          return response.json();
        })
        .then(function (result) {
          if (!result.success) throw new Error(result.message || "Não foi possível enviar a imagem.");

          hiddenInput.value = result.url;
          setPreview(result.url);
        })
        .catch(function (error) {
          notifyError(error.message || "Não foi possível enviar a imagem.");
          resetPlaceholder();
        });
    }

    dropzone.addEventListener("click", function (event) {
      if (removeButton && removeButton.contains(event.target)) return;
      fileInput.click();
    });

    fileInput.addEventListener("change", function () {
      upload(fileInput.files[0]);
    });

    dropzone.addEventListener("dragover", function (event) {
      event.preventDefault();
      dropzone.classList.add("dragover");
    });

    dropzone.addEventListener("dragleave", function () {
      dropzone.classList.remove("dragover");
    });

    dropzone.addEventListener("drop", function (event) {
      event.preventDefault();
      dropzone.classList.remove("dragover");
      upload(event.dataTransfer.files[0]);
    });

    if (removeButton) {
      removeButton.addEventListener("click", function (event) {
        event.stopPropagation();
        fileInput.value = "";
        hiddenInput.value = "";
        resetPlaceholder();
        setPreview("");
      });
    }

    setPreview(hiddenInput.value || "");
  }

  window.AdminImageUpload = { init: init };
})();

// Declarative SweetAlert modal handler for public pages.
(function () {
  "use strict";

  document.addEventListener("click", function (event) {
    var button = event.target.closest("[data-modal]");
    if (!button) return;

    var overlay = document.getElementById(button.dataset.modal);
    if (!overlay || typeof window.SwalTP === "undefined") return;

    event.preventDefault();

    var titleElement = overlay.querySelector(".modal-title");
    var bodyElement = overlay.querySelector(".modal-body");
    var confirmElement = overlay.querySelector(".btn-modal-primary");

    window.SwalTP.fire({
      title: titleElement ? titleElement.textContent.trim() : "",
      html: bodyElement ? bodyElement.innerHTML : "",
      showCancelButton: true,
      confirmButtonText: confirmElement ? confirmElement.textContent.trim() : "Confirmar",
      cancelButtonText: "Cancelar",
    });
  });
})();

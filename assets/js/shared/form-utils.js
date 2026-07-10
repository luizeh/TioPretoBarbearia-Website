// Shared form utilities.
(function () {
  "use strict";

  function aplicarMascaraTelefone(input) {
    if (!input || input.dataset.phoneMaskBound === "true") return;

    input.dataset.phoneMaskBound = "true";
    input.addEventListener("input", function () {
      var valor = this.value.replace(/\D/g, "");

      if (valor.startsWith("55")) valor = valor.substring(2);
      valor = valor.substring(0, 11);

      var formatado = "+55 ";
      if (valor.length > 0) formatado += "(" + valor.substring(0, 2);
      if (valor.length >= 3) formatado += ") " + valor.substring(2, 7);
      if (valor.length >= 8) formatado += "-" + valor.substring(7, 11);

      this.value = formatado;
    });
  }

  window.aplicarMascaraTelefone = aplicarMascaraTelefone;
  document.querySelectorAll('[type="tel"]').forEach(aplicarMascaraTelefone);
})();

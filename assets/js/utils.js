// utils.js — Funções utilitárias compartilhadas

(function () {
  function aplicarMascaraTelefone(input) {
    input.addEventListener("input", function () {
      let valor = this.value.replace(/\D/g, "");

      if (valor.startsWith("55")) {
        valor = valor.substring(2);
      }
      valor = valor.substring(0, 11);

      let formatado = "+55 ";

      if (valor.length > 0) {
        formatado += "(" + valor.substring(0, 2);
      }

      if (valor.length >= 3) {
        formatado += ") " + valor.substring(2, 7);
      }

      if (valor.length >= 8) {
        formatado += "-" + valor.substring(7, 11);
      }

      this.value = formatado;
    });
  }

  document.querySelectorAll('[type="tel"]').forEach(aplicarMascaraTelefone);
})();

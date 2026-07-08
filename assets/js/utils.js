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

  // Expõe para uso em swal-modals.js (reaplicar dentro de popups Swal)
  window.aplicarMascaraTelefone = aplicarMascaraTelefone;

  document.querySelectorAll('[type="tel"]').forEach(aplicarMascaraTelefone);
})();

// ── Filtro de tabela (client-side) ──
document.querySelectorAll("[data-search]").forEach((input) => {
  const table = document.getElementById(input.dataset.search);
  if (!table) return;

  input.addEventListener("input", () => {
    const q = input.value.toLowerCase().trim();
    table.querySelectorAll("tbody tr").forEach((row) => {
      row.style.display = row.textContent.toLowerCase().includes(q)
        ? ""
        : "none";
    });
  });
});

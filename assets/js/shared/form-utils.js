// Shared form utilities.
(function () {
  "use strict";

  function _formatar(valor) {
    valor = valor.replace(/\D/g, "");
    if (valor.startsWith("55")) valor = valor.substring(2);
    valor = valor.substring(0, 11);

    var formatado = "+55 ";
    if (valor.length > 0) formatado += "(" + valor.substring(0, 2);
    if (valor.length >= 3) formatado += ") " + valor.substring(2, 7);
    if (valor.length >= 8) formatado += "-" + valor.substring(7, 11);

    return formatado;
  }

  // Event delegation — formata qualquer [type="tel"] ao digitar,
  // incluindo inputs injetados dinamicamente (ex.: SweetAlert).
  document.addEventListener("input", function (e) {
    if (e.target && e.target.type === "tel") {
      e.target.value = _formatar(e.target.value);
    }
  });

  // Formata o valor atual do input imediatamente.
  // Útil ao pré-preencher via .value = ... seguido de dispatchEvent('input'),
  // ou ao inicializar inputs estáticos já com valor.
  function aplicarMascaraTelefone(input) {
    if (!input) return;
    if (input.value) input.value = _formatar(input.value);
  }

  window.aplicarMascaraTelefone = aplicarMascaraTelefone;
  document.querySelectorAll('[type="tel"]').forEach(aplicarMascaraTelefone);
})();

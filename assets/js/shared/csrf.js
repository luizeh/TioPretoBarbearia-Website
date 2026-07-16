// csrf.js — injeta o token CSRF em toda requisição fetch de escrita (mesma origem).
// Carregado ANTES dos scripts de página (defer preserva a ordem), então cobre
// todos os fetch() existentes (admin, user e auth) sem alterar cada módulo.
// O servidor valida via helpers::verificarCsrf() (header X-CSRF-Token, $_POST ou corpo).
(function () {
  "use strict";

  var meta = document.querySelector('meta[name="csrf-token"]');
  var token = meta ? meta.getAttribute("content") : "";
  if (!token || typeof window.fetch !== "function") return;

  var origem = window.location.origin;
  var fetchOriginal = window.fetch;

  window.fetch = function (input, init) {
    init = init || {};

    // Método efetivo (init tem prioridade; senão o do Request; padrão GET).
    var metodo = (init.method
      || (input && typeof input === "object" && input.method)
      || "GET").toUpperCase();

    // URL alvo (string ou Request).
    var url = typeof input === "string" ? input : (input && input.url) || "";
    // Mesma origem: relativa (não começa com http) ou aponta para a nossa origem.
    var mesmaOrigem = url.indexOf("http://") !== 0 && url.indexOf("https://") !== 0
      ? true
      : url.indexOf(origem) === 0;

    var precisaToken = metodo !== "GET" && metodo !== "HEAD" && metodo !== "OPTIONS";

    if (precisaToken && mesmaOrigem) {
      var headers = new Headers(
        init.headers || (input && typeof input === "object" && input.headers) || {}
      );
      if (!headers.has("X-CSRF-Token")) {
        headers.set("X-CSRF-Token", token);
      }
      init.headers = headers;
    }

    return fetchOriginal.call(this, input, init);
  };
})();

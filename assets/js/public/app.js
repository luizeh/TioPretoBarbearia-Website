// Shared configuration for public pages.
(function () {
  "use strict";

  var body = document.body;
  window.API_BASE = (body && body.dataset.apiBase) || "../api/";
})();

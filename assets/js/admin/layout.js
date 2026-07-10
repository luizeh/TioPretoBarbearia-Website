// Interacoes compartilhadas do layout administrativo.
(function () {
  "use strict";

  var topbarDate = document.getElementById("topbarDate");
  if (topbarDate) {
    var date = new Date();
    var options = {
      weekday: "long",
      day: "2-digit",
      month: "long",
      year: "numeric",
    };

    topbarDate.textContent = date
      .toLocaleDateString("pt-BR", options)
      .replace(/^\w/, function (letter) {
        return letter.toUpperCase();
      });
  }

  var sidebarToggle = document.getElementById("sidebarToggle");
  var sidebar = document.getElementById("sidebar");
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", function () {
      sidebar.classList.toggle("sidebar--open");
    });
  }
})();

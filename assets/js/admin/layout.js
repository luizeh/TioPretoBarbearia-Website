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
  var overlay = document.getElementById("sidebarOverlay");

  if (sidebarToggle && sidebar) {
    function abrirSidebar() {
      sidebar.classList.add("sidebar--open");
      if (overlay) overlay.classList.add("sidebar-overlay--visible");
      document.body.classList.add("sidebar-lock");
    }

    function fecharSidebar() {
      sidebar.classList.remove("sidebar--open");
      if (overlay) overlay.classList.remove("sidebar-overlay--visible");
      document.body.classList.remove("sidebar-lock");
    }

    sidebarToggle.addEventListener("click", function () {
      if (sidebar.classList.contains("sidebar--open")) {
        fecharSidebar();
      } else {
        abrirSidebar();
      }
    });

    // Fecha ao clicar no overlay
    if (overlay) overlay.addEventListener("click", fecharSidebar);

    // Fecha ao escolher um item do menu (navegação no mobile)
    sidebar.querySelectorAll(".nav-item").forEach(function (link) {
      link.addEventListener("click", fecharSidebar);
    });

    // Fecha com a tecla Esc
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") fecharSidebar();
    });
  }
})();

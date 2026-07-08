// public.js — Scripts compartilhados de todas as páginas públicas
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ── User menu toggle ─────────────────────────────────
    var userMenu = document.getElementById("headerUserMenu");
    if (userMenu) {
      userMenu
        .querySelector(".user-menu__trigger")
        .addEventListener("click", function (e) {
          e.stopPropagation();
          userMenu.classList.toggle("open");
        });
      document.addEventListener("click", function () {
        userMenu.classList.remove("open");
      });
    }

    // ── Scroll fade-in (IntersectionObserver) ────────────
    if ("IntersectionObserver" in window) {
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add("visible");
              obs.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.1 },
      );
      document.querySelectorAll(".fade-in").forEach(function (el) {
        obs.observe(el);
      });
    }

    // ── Modais via SweetAlert2 ────────────────────────────
    document.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-modal]");
      if (!btn) return;

      var id = btn.dataset.modal;
      var overlay = document.getElementById(id);
      if (!overlay) return;

      e.preventDefault();

      var titleEl = overlay.querySelector(".modal-title");
      var title = titleEl ? titleEl.textContent.trim() : "";
      var bodyHTML = overlay.querySelector(".modal-body")
        ? overlay.querySelector(".modal-body").innerHTML
        : "";
      var footerEl = overlay.querySelector(".btn-modal-primary");
      var confirmText = footerEl ? footerEl.textContent.trim() : "Confirmar";

      if (typeof window.SwalTP !== "undefined") {
        SwalTP.fire({
          title: title,
          html: bodyHTML,
          showCancelButton: true,
          confirmButtonText: confirmText,
          cancelButtonText: "Cancelar",
        });
      }
    });
  });
})();

// Interacoes compartilhadas do cabecalho publico.
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ── Menu mobile (hamburger) ──
    var menuToggle = document.getElementById("mobileMenuToggle");
    var headerEl = document.querySelector("header");
    if (menuToggle && headerEl) {
      var setBurger = function (open) {
        headerEl.classList.toggle("nav-open", open);
        menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
        var icon = menuToggle.querySelector("i");
        if (icon) icon.className = open ? "fa-solid fa-xmark" : "fa-solid fa-bars";
      };
      menuToggle.addEventListener("click", function (event) {
        event.stopPropagation();
        setBurger(!headerEl.classList.contains("nav-open"));
      });
      headerEl.querySelectorAll("nav a").forEach(function (link) {
        link.addEventListener("click", function () {
          setBurger(false);
        });
      });
      document.addEventListener("click", function (event) {
        if (headerEl.classList.contains("nav-open") && !headerEl.contains(event.target)) {
          setBurger(false);
        }
      });
      document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") setBurger(false);
      });
    }

    var userMenu = document.getElementById("headerUserMenu");
    if (userMenu) {
      var userTrigger = userMenu.querySelector(".user-menu__trigger");
      if (userTrigger) {
        userTrigger.addEventListener("click", function (event) {
          event.stopPropagation();
          userMenu.classList.toggle("open");
          // Abrir um painel fecha os demais.
          var cart = document.getElementById("headerCart");
          if (cart) cart.classList.remove("open");
          var notif = document.getElementById("headerNotifications");
          if (notif) notif.classList.remove("open");
        });
      }

      document.addEventListener("click", function () {
        userMenu.classList.remove("open");
      });
    }

    var notificationsMenu = document.getElementById("headerNotifications");
    if (notificationsMenu) {
      var notificationsTrigger = notificationsMenu.querySelector(".notifications-menu__trigger");
      if (notificationsTrigger) {
        notificationsTrigger.addEventListener("click", function (event) {
          event.stopPropagation();
          notificationsMenu.classList.toggle("open");
          // Abrir um painel fecha os demais.
          var cart = document.getElementById("headerCart");
          if (cart) cart.classList.remove("open");
          var user = document.getElementById("headerUserMenu");
          if (user) user.classList.remove("open");
        });
      }

      notificationsMenu.querySelectorAll("[data-header-notification]").forEach(function (item) {
        item.addEventListener("click", function () {
          fetch((window.API_BASE || "../../api/") + "user/notificacoes.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "marcar_lida", id: item.dataset.headerNotification }),
          }).then(function () {
            item.classList.remove("is-unread");
          });
        });
      });

      var readAll = document.getElementById("headerNotificationsReadAll");
      if (readAll) {
        readAll.addEventListener("click", function () {
          fetch((window.API_BASE || "../../api/") + "user/notificacoes.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "marcar_todas" }),
          }).then(function () {
            notificationsMenu.querySelectorAll(".is-unread").forEach(function (item) {
              item.classList.remove("is-unread");
            });

            var badge = notificationsMenu.querySelector(".notifications-menu__badge");
            if (badge) badge.remove();
          });
        });
      }

      document.addEventListener("click", function () {
        notificationsMenu.classList.remove("open");
      });
    }

    // Esc fecha os painéis abertos do header (carrinho, notificações, perfil).
    document.addEventListener("keydown", function (event) {
      if (event.key !== "Escape") return;
      ["headerCart", "headerNotifications", "headerUserMenu"].forEach(function (id) {
        var panel = document.getElementById(id);
        if (panel) panel.classList.remove("open");
      });
    });

    document.querySelectorAll("[data-hide-on-error]").forEach(function (image) {
      image.addEventListener("error", function () {
        image.hidden = true;
      });
    });

    if (!("IntersectionObserver" in window)) return;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;

          entry.target.classList.add("visible");
          observer.unobserve(entry.target);
        });
      },
      { threshold: 0.1 },
    );

    document.querySelectorAll(".fade-in").forEach(function (element) {
      observer.observe(element);
    });
  });
})();

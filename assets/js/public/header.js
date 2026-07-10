// Interacoes compartilhadas do cabecalho publico.
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var userMenu = document.getElementById("headerUserMenu");
    if (userMenu) {
      var userTrigger = userMenu.querySelector(".user-menu__trigger");
      if (userTrigger) {
        userTrigger.addEventListener("click", function (event) {
          event.stopPropagation();
          userMenu.classList.toggle("open");
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

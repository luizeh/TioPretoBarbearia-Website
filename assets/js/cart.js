// cart.js — Carrinho estático (visual only)
(function () {
  document.addEventListener("DOMContentLoaded", function () {
    // ── Toggle do dropdown do carrinho ───────────────────────
    var cartMenu = document.getElementById("headerCart");
    if (cartMenu) {
      cartMenu
        .querySelector(".cart-menu__trigger")
        .addEventListener("click", function (e) {
          e.stopPropagation();
          cartMenu.classList.toggle("open");
          var um = document.getElementById("headerUserMenu");
          if (um) um.classList.remove("open");
        });
      document.addEventListener("click", function () {
        cartMenu.classList.remove("open");
      });
      cartMenu
        .querySelector(".cart-menu__dropdown")
        .addEventListener("click", function (e) {
          e.stopPropagation();
        });
    }

    // ── Feedback "Adicionado!" nos botões dos cards ──────────
    document.querySelectorAll("[data-add-cart]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var orig = this.innerHTML;
        this.innerHTML = '<i class="fa-solid fa-check"></i> Adicionado!';
        this.disabled = true;
        var self = this;
        setTimeout(function () {
          self.innerHTML = orig;
          self.disabled = false;
        }, 1500);
      });
    });
  });
})();

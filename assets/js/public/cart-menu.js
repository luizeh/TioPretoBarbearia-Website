// Carrinho persistente exibido no cabeçalho das páginas públicas.
(function () {
  "use strict";

  var base = window.API_BASE || "../api/";
  var loggedIn = false;

  function cartUrl() {
    return base + "user/carrinho.php";
  }

  function escapeHtml(value) {
    var element = document.createElement("div");
    element.textContent = value || "";
    return element.innerHTML;
  }

  function formatCurrency(value) {
    return "R$ " + Number(value).toLocaleString("pt-BR", { minimumFractionDigits: 2 });
  }

  function setBadge(quantity) {
    var badge = document.getElementById("cartBadge");
    if (!badge) return;

    badge.textContent = quantity > 0 ? quantity : "0";
    badge.classList.toggle("has-items", quantity > 0);
  }

  function bindImageErrors(container) {
    container.querySelectorAll("[data-cart-image]").forEach(function (image) {
      image.addEventListener("error", function () {
        image.hidden = true;
      });
    });
  }

  function renderItem(item) {
    var imageUrl = item.foto_url ? base.replace("api/", "") + item.foto_url : "";
    var image = imageUrl
      ? '<img class="cart-item__image" data-cart-image src="' + escapeHtml(imageUrl) + '" alt="">'
      : '<div class="cart-item__placeholder" aria-hidden="true"></div>';

    return (
      '<article class="cart-item">' +
      image +
      '<div class="cart-item__details">' +
      '<strong class="cart-item__name">' + escapeHtml(item.nome) + "</strong>" +
      '<span class="cart-item__price">' + formatCurrency(item.preco) + "</span>" +
      "</div>" +
      '<div class="cart-item__controls">' +
      '<button class="cart-item__quantity-button" type="button" data-cart-dec data-item-id="' + item.id + '" data-qty="' + (item.quantidade - 1) + '" aria-label="Diminuir quantidade">−</button>' +
      '<span class="cart-item__quantity">' + item.quantidade + "</span>" +
      '<button class="cart-item__quantity-button" type="button" data-cart-inc data-item-id="' + item.id + '" data-qty="' + (item.quantidade + 1) + '" aria-label="Aumentar quantidade">+</button>' +
      "</div>" +
      '<button class="cart-item__remove cart-item__remove--danger" type="button" data-cart-remove data-item-id="' + item.id + '" title="Remover" aria-label="Remover item"><i class="fa-solid fa-trash"></i></button>' +
      "</article>"
    );
  }

  function renderCart(data) {
    var items = data.itens || [];
    var itemsElement = document.getElementById("cartItems");
    var emptyElement = document.getElementById("cartEmpty");
    var footerElement = document.getElementById("cartFooter");
    if (!itemsElement) return;

    setBadge(data.count || 0);

    var isEmpty = items.length === 0;
    if (emptyElement) emptyElement.classList.toggle("is-visible", isEmpty);
    if (footerElement) footerElement.classList.toggle("is-visible", !isEmpty);

    if (isEmpty) {
      itemsElement.innerHTML = "";
      return;
    }

    itemsElement.innerHTML =
      items.map(renderItem).join("") +
      '<div class="cart-menu__total">Total: ' + formatCurrency(data.total || 0) + "</div>";
    bindImageErrors(itemsElement);
  }

  function fetchCart() {
    if (!loggedIn) return;

    fetch(cartUrl())
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (result.success) renderCart(result.data);
      })
      .catch(function () {});
  }

  function postCart(payload) {
    return fetch(cartUrl(), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    }).then(function (response) {
      return response.json();
    });
  }

  function updateCartItem(action, button) {
    postCart({
      action: action,
      item_id: button.dataset.itemId,
      quantidade: Number(button.dataset.qty),
    }).then(function (result) {
      if (result.success) renderCart(result.data);
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    loggedIn = Boolean(document.querySelector(".user-avatar"));

    var cartMenu = document.getElementById("headerCart");
    if (cartMenu) {
      var trigger = cartMenu.querySelector(".cart-menu__trigger");
      var dropdown = cartMenu.querySelector(".cart-menu__dropdown");

      if (trigger) {
        trigger.addEventListener("click", function (event) {
          event.stopPropagation();
          cartMenu.classList.toggle("open");

          var userMenu = document.getElementById("headerUserMenu");
          if (userMenu) userMenu.classList.remove("open");
          if (cartMenu.classList.contains("open")) fetchCart();
        });
      }

      document.addEventListener("click", function () {
        cartMenu.classList.remove("open");
      });

      if (dropdown) {
        dropdown.addEventListener("click", function (event) {
          event.stopPropagation();
        });
      }
    }

    var itemsElement = document.getElementById("cartItems");
    if (itemsElement) {
      itemsElement.addEventListener("click", function (event) {
        var increase = event.target.closest("[data-cart-inc]");
        var decrease = event.target.closest("[data-cart-dec]");
        var remove = event.target.closest("[data-cart-remove]");

        if (increase) updateCartItem("atualizar", increase);
        if (decrease) updateCartItem(Number(decrease.dataset.qty) <= 0 ? "remover" : "atualizar", decrease);
        if (remove) updateCartItem("remover", remove);
      });
    }

    var clearButton = document.getElementById("cartClear");
    if (clearButton) {
      clearButton.addEventListener("click", function () {
        postCart({ action: "limpar" }).then(function (result) {
          if (result.success) renderCart(result.data);
        });
      });
    }

    document.querySelectorAll("[data-add-cart]").forEach(function (button) {
      button.addEventListener("click", function () {
        if (!loggedIn) {
          var publicBase = window.API_BASE ? window.API_BASE.replace("api/", "") : "../";
          window.SwalTP.fire({
            icon: "info",
            title: "Faça login",
            text: "Para adicionar ao carrinho, faça login primeiro.",
            confirmButtonText: "Entrar",
          }).then(function (result) {
            if (result.isConfirmed) window.location.href = publicBase + "view/login.php";
          });
          return;
        }

        var productId = button.dataset.productId || button.dataset.addCart;
        if (!productId) return;

        var originalContent = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        button.disabled = true;

        postCart({ action: "adicionar", produto_id: productId })
          .then(function (result) {
            if (!result.success) return;

            setBadge(result.data.count);
            button.innerHTML = '<i class="fa-solid fa-check"></i> Adicionado!';
          })
          .finally(function () {
            window.setTimeout(function () {
              button.innerHTML = originalContent;
              button.disabled = false;
            }, 1500);
          });
      });
    });

    // "Finalizar Pedido" é um link direto para a página do carrinho
    // (onde fica o formulário de endereço) — sem modal.

    fetchCart();
  });
})();

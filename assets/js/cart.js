// cart.js — Carrinho com persistência via API
(function () {
  var BASE = window.API_BASE || "../api/";
  var loggedIn = false;

  function getCarrinhoUrl() {
    return BASE + "user/carrinho.php";
  }
  function getPedidosUrl() {
    return BASE + "user/pedidos.php";
  }

  function fmt(val) {
    return (
      "R$ " + Number(val).toLocaleString("pt-BR", { minimumFractionDigits: 2 })
    );
  }

  // ── UI helpers ────────────────────────────────────────────────
  function setBadge(n) {
    var el = document.getElementById("cartBadge");
    if (el) el.textContent = n > 0 ? n : "0";
  }

  function renderCart(data) {
    var itens = data.itens || [];
    var total = data.total || 0;
    var count = data.count || 0;

    setBadge(count);

    var emptyEl = document.getElementById("cartEmpty");
    var itemsEl = document.getElementById("cartItems");
    var footerEl = document.getElementById("cartFooter");

    if (!itemsEl) return;

    if (itens.length === 0) {
      if (emptyEl) emptyEl.style.display = "";
      if (footerEl) footerEl.style.display = "none";
      itemsEl.innerHTML = "";
      return;
    }

    if (emptyEl) emptyEl.style.display = "none";
    if (footerEl) footerEl.style.display = "";

    itemsEl.innerHTML =
      itens
        .map(function (item) {
          var imgSrc = item.foto_url
            ? BASE.replace("api/", "") + item.foto_url
            : "";
          var imgTag = imgSrc
            ? '<img src="' +
              imgSrc +
              '" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:4px;flex-shrink:0;" onerror="this.style.display=\'none\'">'
            : '<div style="width:44px;height:44px;background:#f0ece6;border-radius:4px;flex-shrink:0;"></div>';
          return (
            '<div class="cart-item" style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--border);">' +
            imgTag +
            '<div style="flex:1;min-width:0;">' +
            '<div style="font-size:.88rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' +
            item.nome +
            "</div>" +
            '<div style="font-size:.82rem;color:var(--gold);">' +
            fmt(item.preco) +
            "</div>" +
            "</div>" +
            '<div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">' +
            '<button data-cart-dec data-item-id="' +
            item.id +
            '" data-qty="' +
            (item.quantidade - 1) +
            '" style="width:26px;height:26px;border:1px solid var(--border);border-radius:3px;background:none;cursor:pointer;font-size:1rem;line-height:1;">−</button>' +
            '<span style="min-width:20px;text-align:center;font-size:.88rem;">' +
            item.quantidade +
            "</span>" +
            '<button data-cart-inc data-item-id="' +
            item.id +
            '" data-qty="' +
            (item.quantidade + 1) +
            '" style="width:26px;height:26px;border:1px solid var(--border);border-radius:3px;background:none;cursor:pointer;font-size:1rem;line-height:1;">+</button>' +
            "</div>" +
            '<button data-cart-remove data-item-id="' +
            item.id +
            '" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:.9rem;padding:4px;" title="Remover"><i class="fa-solid fa-trash"></i></button>' +
            "</div>"
          );
        })
        .join("") +
      '<div style="padding:12px 16px;font-weight:700;font-size:.9rem;text-align:right;border-top:2px solid var(--border);">Total: ' +
      fmt(total) +
      "</div>";
  }

  function fetchCart() {
    if (!loggedIn) return;
    fetch(getCarrinhoUrl())
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (res.success) renderCart(res.data);
      })
      .catch(function () {});
  }

  function postCart(payload) {
    return fetch(getCarrinhoUrl(), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    }).then(function (r) {
      return r.json();
    });
  }

  // ── Bootstrap ────────────────────────────────────────────────
  document.addEventListener("DOMContentLoaded", function () {
    // Detecta se há usuário logado (avatar no header existe)
    var avatar = document.querySelector(".user-avatar");
    loggedIn = !!avatar;

    // Toggle do dropdown
    var cartMenu = document.getElementById("headerCart");
    if (cartMenu) {
      cartMenu
        .querySelector(".cart-menu__trigger")
        .addEventListener("click", function (e) {
          e.stopPropagation();
          cartMenu.classList.toggle("open");
          var um = document.getElementById("headerUserMenu");
          if (um) um.classList.remove("open");
          if (cartMenu.classList.contains("open")) fetchCart();
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

    // Delegação: qty inc/dec/remove
    var itemsEl = document.getElementById("cartItems");
    if (itemsEl) {
      itemsEl.addEventListener("click", function (e) {
        var inc = e.target.closest("[data-cart-inc]");
        var dec = e.target.closest("[data-cart-dec]");
        var rem = e.target.closest("[data-cart-remove]");
        if (inc) {
          postCart({
            action: "atualizar",
            item_id: inc.dataset.itemId,
            quantidade: parseInt(inc.dataset.qty),
          }).then(function (r) {
            if (r.success) renderCart(r.data);
          });
        }
        if (dec) {
          postCart({
            action: dec.dataset.qty <= 0 ? "remover" : "atualizar",
            item_id: dec.dataset.itemId,
            quantidade: parseInt(dec.dataset.qty),
          }).then(function (r) {
            if (r.success) renderCart(r.data);
          });
        }
        if (rem) {
          postCart({ action: "remover", item_id: rem.dataset.itemId }).then(
            function (r) {
              if (r.success) renderCart(r.data);
            },
          );
        }
      });
    }

    // Limpar
    var clearBtn = document.getElementById("cartClear");
    if (clearBtn) {
      clearBtn.addEventListener("click", function () {
        postCart({ action: "limpar" }).then(function (r) {
          if (r.success) renderCart(r.data);
        });
      });
    }

    // Botões "Adicionar" nos cards de produto
    document.querySelectorAll("[data-add-cart]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (!loggedIn) {
          var base = window.API_BASE
            ? window.API_BASE.replace("api/", "")
            : "../";
          if (typeof SwalTP !== "undefined") {
            SwalTP.fire({
              icon: "info",
              title: "Faça login",
              text: "Para adicionar ao carrinho, faça login primeiro.",
              confirmButtonText: "Entrar",
            }).then(function (r) {
              if (r.isConfirmed) window.location.href = base + "view/login.php";
            });
          }
          return;
        }
        var prodId = btn.dataset.productId || btn.dataset.addCart;
        if (!prodId) return;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;
        postCart({ action: "adicionar", produto_id: prodId })
          .then(function (r) {
            if (r.success) {
              setBadge(r.data.count);
              btn.innerHTML = '<i class="fa-solid fa-check"></i> Adicionado!';
              setTimeout(function () {
                btn.innerHTML = orig;
                btn.disabled = false;
              }, 1500);
            } else {
              btn.innerHTML = orig;
              btn.disabled = false;
            }
          })
          .catch(function () {
            btn.innerHTML = orig;
            btn.disabled = false;
          });
      });
    });

    // Finalizar pedido
    var checkoutBtn = document.querySelector(".btn-cart-checkout");
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!loggedIn) return;
        if (typeof SwalTP === "undefined") return;
        SwalTP.fire({
          title: "Finalizar Pedido",
          html: '<div class="modal-field"><label class="modal-label">Endereço de Entrega</label><textarea class="modal-textarea" id="swal-endereco" placeholder="Rua, número, bairro, cidade..." rows="3" style="min-height:80px;"></textarea></div>',
          showCancelButton: true,
          confirmButtonText: "Encomendar",
          cancelButtonText: "Cancelar",
          preConfirm: function () {
            var end = document.getElementById("swal-endereco").value.trim();
            if (!end) {
              Swal.showValidationMessage("Informe o endereço.");
              return false;
            }
            return fetch(getPedidosUrl(), {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ action: "finalizar", endereco: end }),
            }).then(function (r) {
              return r.json();
            });
          },
        }).then(function (result) {
          if (!result.isConfirmed) return;
          if (result.value && result.value.success) {
            renderCart({ itens: [], total: 0, count: 0 });
            SwalTP.fire({
              icon: "success",
              title: "Pedido realizado!",
              text: result.value.message,
              timer: 3000,
              showConfirmButton: false,
            });
          } else {
            SwalTP.fire({
              icon: "error",
              title: "Erro",
              text:
                (result.value && result.value.message) ||
                "Não foi possível finalizar.",
            });
          }
        });
      });
    }

    // Carregar carrinho inicial
    fetchCart();
  });
})();

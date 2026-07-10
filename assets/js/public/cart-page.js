(function () {
  var base = window.API_BASE || '../../api/';
  function request(payload) {
    return fetch(base + 'user/carrinho.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).then(function (r) { return r.json(); });
  }
  function refresh() { window.location.reload(); }
  document.addEventListener('click', function (event) {
    var inc = event.target.closest('[data-page-inc]');
    var dec = event.target.closest('[data-page-dec]');
    var remove = event.target.closest('[data-page-remove]');
    if (inc || dec) {
      var id = (inc || dec).dataset.pageInc || (inc || dec).dataset.pageDec;
      var row = (inc || dec).closest('[data-cart-page-item]');
      var current = Number(row.querySelector('.cart-page-item__quantity strong').textContent);
      request({ action: 'atualizar', item_id: id, quantidade: inc ? current + 1 : current - 1 }).then(function (r) { if (r.success) refresh(); else SwalTP.fire({ icon: 'error', title: 'Não foi possível atualizar', text: r.message }); });
    }
    if (remove) request({ action: 'remover', item_id: remove.dataset.pageRemove }).then(function (r) { if (r.success) refresh(); });
  });
  var checkout = document.getElementById('cart-page-checkout');
  if (checkout) checkout.addEventListener('click', function () {
    var endereco = document.getElementById('cart-page-address').value.trim();
    if (!endereco) { SwalTP.fire({ icon: 'warning', title: 'Informe o endereço', text: 'Precisamos do endereço para entregar o pedido.' }); return; }
    checkout.disabled = true;
    fetch(base + 'user/pedidos.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'finalizar', endereco: endereco }) }).then(function (r) { return r.json(); }).then(function (resposta) {
      if (!resposta.success) { checkout.disabled = false; SwalTP.fire({ icon: 'error', title: 'Não foi possível fazer o pedido', text: resposta.message }); return; }
      SwalTP.fire({ icon: 'success', title: 'Pedido realizado!', text: resposta.message, confirmButtonText: 'Ver meus pedidos' }).then(function () { window.location.href = 'pedidos.php'; });
    });
  });
})();

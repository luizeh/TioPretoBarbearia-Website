(function () {
  var base = window.API_BASE || '../../api/';
  function enviar(payload) {
    return fetch(base + 'user/notificacoes.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).then(function (response) { return response.json(); });
  }
  document.addEventListener('click', function (event) {
    var individual = event.target.closest('[data-mark-notification]');
    var todas = event.target.closest('#marcar-todas-notificacoes');
    if (individual) enviar({ action: 'marcar_lida', id: individual.dataset.markNotification }).then(function () { individual.closest('.notification-item').classList.remove('notification-item--unread'); individual.remove(); });
    if (todas) enviar({ action: 'marcar_todas' }).then(function () { document.querySelectorAll('.notification-item--unread').forEach(function (item) { item.classList.remove('notification-item--unread'); }); document.querySelectorAll('[data-mark-notification]').forEach(function (button) { button.remove(); }); todas.remove(); });
  });
})();

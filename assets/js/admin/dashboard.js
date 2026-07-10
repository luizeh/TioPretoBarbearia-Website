// dashboard-page.js — Carrega estatísticas reais no dashboard admin

(function () {
  fetch("../../api/admin/dashboard.php")
    .then(function (r) {
      return r.json();
    })
    .then(function (res) {
      if (!res.success || !res.data) return;
      var s = res.data.stats;
      var elClientes = document.getElementById("statClientes");
      var elAgend = document.getElementById("statAgendamentos");
      var elNovos = document.getElementById("statNovos");
      if (elClientes) elClientes.textContent = s.total_clientes;
      if (elAgend) elAgend.textContent = s.agendamentos_hoje;
      if (elNovos) elNovos.textContent = s.novos_mes;
    })
    .catch(function () {});
})();

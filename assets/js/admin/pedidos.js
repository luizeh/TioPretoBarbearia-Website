// Atualização de status dos pedidos no painel administrativo.
(function () {
  "use strict";

  document.querySelectorAll("[data-pedido-id]").forEach(function (select) {
    select.dataset.previous = select.value;
  });

  document.addEventListener("change", function (event) {
    var select = event.target.closest("[data-pedido-id]");
    if (!select) return;

    var previousStatus = select.dataset.previous || select.value;
    var nextStatus = select.value;
    select.disabled = true;

    fetch("../../api/admin/pedidos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: select.dataset.pedidoId, status: nextStatus }),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (result.success) {
          select.dataset.previous = nextStatus;
          window.SwalTP.fire({
            icon: "success",
            title: "Status atualizado",
            timer: 1300,
            showConfirmButton: false,
          });
          return;
        }

        select.value = previousStatus;
        window.SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: result.message || "Não foi possível atualizar.",
        });
      })
      .catch(function () {
        select.value = previousStatus;
        window.SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: "Falha de conexão.",
        });
      })
      .finally(function () {
        select.disabled = false;
      });
  });
})();

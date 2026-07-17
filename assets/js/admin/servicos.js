// CRUD da página de serviços do painel administrativo.
(function () {
  "use strict";

  function post(payload) {
    return fetch("../../api/admin/servicos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    }).then(function (response) {
      return response.json();
    });
  }

  function readForm(popup) {
    function value(name) {
      return (popup.querySelector('[data-field="' + name + '"]') || {}).value || "";
    }

    return {
      nome: value("nome").trim(),
      duracao: value("duracao"),
      preco: value("preco"),
      descricao: value("descricao").trim(),
      foto_url: value("foto_url"),
    };
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest('[data-modal="modal-servico"]');
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    var overlay = document.getElementById("modal-servico");
    if (!overlay) return;

    var isEdit = Boolean(button.dataset.id);
    window.SwalTP.fire({
      title: isEdit ? "Editar Serviço" : "Novo Serviço",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      width: "640px",
      didOpen: function (popup) {
        ["nome", "duracao", "preco", "descricao", "foto_url"].forEach(function (key) {
          var field = popup.querySelector('[data-field="' + key + '"]');
          if (field) field.value = button.dataset[key] || "";
        });

        if (window.AdminImageUpload) window.AdminImageUpload.init(popup);
      },
      preConfirm: function () {
        var data = readForm(window.Swal.getPopup());
        if (!data.nome || !data.preco || !data.duracao) {
          window.Swal.showValidationMessage("Nome, preço e duração são obrigatórios.");
          return false;
        }

        var payload = {
          action: isEdit ? "editar" : "criar",
          nome: data.nome,
          preco: data.preco,
          tempo_estimado: data.duracao,
          descricao: data.descricao,
          foto_url: data.foto_url,
        };
        if (isEdit) payload.id = button.dataset.id;
        return post(payload);
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;

      if (result.value && result.value.success) {
        window.SwalTP.fire({
          icon: "success",
          title: isEdit ? "Atualizado!" : "Criado!",
          timer: 1800,
          showConfirmButton: false,
        }).then(function () {
          window.location.reload();
        });
        return;
      }

      window.SwalTP.fire({
        icon: "error",
        title: "Erro",
        text: (result.value && result.value.message) || "Erro ao salvar.",
      });
    });
  });

  function escapeHtml(value) {
    var element = document.createElement("div");
    element.textContent = value || "";
    return element.innerHTML;
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest('[data-modal="modal-servico-excluir"]');
    if (!button) return;

    event.preventDefault();
    event.stopPropagation();

    var nomeServico = escapeHtml(button.dataset.nome || "este serviço");

    // 1) Consulta os agendamentos relacionados antes de confirmar.
    post({ action: "info-exclusao", id: button.dataset.id }).then(function (info) {
      if (!info || !info.success) {
        window.SwalTP.erro("Erro", (info && info.message) || "Não foi possível verificar o serviço.");
        return;
      }

      var total = (info.data && info.data.total) || 0;
      var futuros = (info.data && info.data.futuros) || 0;
      var temHistorico = total > 0;

      var html;
      if (temHistorico) {
        html =
          "O serviço <strong>" + nomeServico + "</strong> possui <strong>" + total +
          " agendamento(s)</strong> relacionados.<br><br>" +
          '<span class="swal-text-muted">Ele será <strong>desativado</strong> — some das listagens e de novos ' +
          "agendamentos, mas o histórico é preservado.</span>";
        if (futuros > 0) {
          html +=
            '<br><br><span class="swal-text-muted"><strong>' + futuros +
            " agendamento(s) futuro(s)</strong> serão <strong>cancelados</strong> ao continuar.</span>";
        }
      } else {
        html =
          "Tem certeza que deseja excluir <strong>" + nomeServico + "</strong>?<br>" +
          '<small class="swal-text-muted">Este serviço não possui agendamentos. Esta ação não pode ser desfeita.</small>';
      }

      window.SwalTP.confirmarExclusao({
        title: temHistorico ? "Desativar serviço?" : "Excluir serviço?",
        html: html,
        confirmButtonText: temHistorico ? "Sim, desativar" : "Sim, excluir",
        cancelButtonText: "Cancelar",
        preConfirm: function () {
          return post({ action: "excluir", id: button.dataset.id });
        },
      }).then(function (result) {
        if (!result.isConfirmed) return;

        if (result.value && result.value.success) {
          window.SwalTP.fire({
            icon: "success",
            title: temHistorico ? "Desativado!" : "Excluído!",
            text: result.value.message || "",
            timer: 2600,
            showConfirmButton: false,
          }).then(function () {
            window.location.reload();
          });
          return;
        }

        window.SwalTP.erro("Erro", (result.value && result.value.message) || "Erro ao excluir.");
      });
    });
  });
})();

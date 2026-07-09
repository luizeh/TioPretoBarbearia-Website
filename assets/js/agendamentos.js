// agendamentos.js — CRUD de agendamentos + WhatsApp Lembrete

(function () {
  // ─── Excluir Agendamento ─────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-agendamento-excluir"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-agendamento-excluir");
    if (!overlay) return;

    SwalTP.fire({
      title: "Excluir Agendamento",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Excluir",
      cancelButtonText: "Cancelar",
      customClass: { confirmButton: "swal-tp__btn swal-tp__btn--danger" },
      didOpen: function (popup) {
        var el = popup.querySelector("[data-field='nome']");
        if (el) el.textContent = btn.dataset.nome || "este agendamento";
      },
      preConfirm: function () {
        return fetch("../../api/admin/agendamentos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "excluir", id: btn.dataset.id }),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Excluído!",
          timer: 1500,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao excluir.",
        });
      }
    });
  });

  // ─── Alterar status do agendamento ───────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-agendamento"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-agendamento");
    if (!overlay) return;

    var isEdit = !!btn.dataset.id;
    if (!isEdit) return; // Novo agendamento: deixa para swal-modals genérico se necessário

    SwalTP.fire({
      title: "Editar Agendamento",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        // Preencher status
        var sel = popup.querySelector("select");
        if (sel && btn.dataset.status) sel.value = btn.dataset.status;
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var sel = popup.querySelector("select");
        var novoStatus = sel ? sel.value : "";
        if (!novoStatus) {
          Swal.showValidationMessage("Selecione um status.");
          return false;
        }
        return fetch("../../api/admin/agendamentos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "status",
            id: btn.dataset.id,
            status: novoStatus,
          }),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Atualizado!",
          timer: 1500,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao atualizar.",
        });
      }
    });
  });

  // ─── Lembrete WhatsApp ───────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-action--whatsapp");
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var nome = btn.dataset.cliente || "";
    var tel = (btn.dataset.telefone || "").replace(/\D/g, "");
    var servico = btn.dataset.servico || "";
    var data = btn.dataset.data || "";
    var hora = btn.dataset.hora || "";

    var msgDefault =
      "Olá, " +
      nome +
      "! 👋\n\nLembramos que você tem um agendamento na *Tio Preto Barbearia*:\n\n" +
      "📋 *Serviço:* " +
      servico +
      "\n" +
      "📅 *Data:* " +
      data +
      "\n" +
      "⏰ *Horário:* " +
      hora +
      "\n\n" +
      "Aguardamos você! 💈\n_Tio Preto Barbearia_";

    SwalTP.fire({
      title:
        '<span style="color:#25d366"><i class="fa-brands fa-whatsapp"></i></span> Lembrete WhatsApp',
      html:
        '<div class="modal-field" style="margin-bottom:12px">' +
        '<label class="modal-label">Para: <strong>' +
        nome +
        "</strong>" +
        (tel
          ? " &nbsp;·&nbsp; +" + tel
          : " <small style='color:#f39c12'>(sem telefone)</small>") +
        "</label></div>" +
        '<div class="modal-field">' +
        '<label class="modal-label">Mensagem</label>' +
        '<textarea id="swal-whatsapp-msg" class="modal-textarea" rows="8" style="resize:vertical;min-height:160px">' +
        msgDefault +
        "</textarea></div>",
      showCancelButton: true,
      confirmButtonText: "Enviar",
      cancelButtonText: "Cancelar",
      width: "560px",
      preConfirm: function () {
        var msg = document.getElementById("swal-whatsapp-msg").value.trim();
        if (!msg) {
          Swal.showValidationMessage("A mensagem não pode estar vazia.");
          return false;
        }
        if (!tel) {
          Swal.showValidationMessage(
            "Este cliente não possui telefone cadastrado.",
          );
          return false;
        }
        return fetch("../../api/admin/whatsapp.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ telefone: tel, mensagem: msg }),
        })
          .then(function (r) {
            return r.json();
          })
          .catch(function () {
            return { success: false, message: "Erro de rede." };
          });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: "Enviado!",
          text: "Lembrete enviado para " + nome + " via WhatsApp.",
          timer: 2500,
          showConfirmButton: false,
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Falha no envio",
          text:
            (result.value && result.value.message) ||
            "Não foi possível enviar o lembrete.",
        });
      }
    });
  });
})();

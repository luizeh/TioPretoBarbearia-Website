// agendamentos.js — CRUD de agendamentos + WhatsApp Lembrete

(function () {
  function escapeHtml(value) {
    var el = document.createElement("div");
    el.textContent = value || "";
    return el.innerHTML;
  }

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
      customClass: {
        popup: "swal-tp",
        title: "swal-tp__title",
        htmlContainer: "swal-tp__body",
        confirmButton: "swal-tp__btn swal-tp__btn--danger",
        cancelButton: "swal-tp__btn swal-tp__btn--cancel",
        actions: "swal-tp__actions",
        closeButton: "swal-tp__close",
      },
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

  // ─── Criar / Alterar agendamento ───────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest('[data-modal="modal-agendamento"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var overlay = document.getElementById("modal-agendamento");
    if (!overlay) return;

    var isEdit = !!btn.dataset.id;

    SwalTP.fire({
      title: isEdit ? "Editar Agendamento" : "Novo Agendamento",
      html: overlay.querySelector(".modal-body").innerHTML,
      showCancelButton: true,
      confirmButtonText: isEdit ? "Salvar" : "Criar",
      cancelButtonText: "Cancelar",
      didOpen: function (popup) {
        var userSel = popup.querySelector('[name="usuario_id"]');
        var serviceInputs = Array.prototype.slice.call(
          popup.querySelectorAll('[name="servicos_ids"]'),
        );
        var dateEl = popup.querySelector('[name="data"]');
        var timeEl = popup.querySelector('[name="hora_inicio"]');
        var statusSel = popup.querySelector('[name="status"]');
        var dataSelecionada = btn.dataset.date || btn.dataset.data || "";
        var horaSelecionada = btn.dataset.time || btn.dataset.hora || "";
        var horarioFoiClicado =
          !isEdit && Boolean(dataSelecionada) && Boolean(horaSelecionada);
        dateEl.value = dataSelecionada;
        timeEl.value = horaSelecionada;
        var periodoCampos = popup.querySelector("[data-periodo-campos]");
        var horarioSelecionado = popup.querySelector(
          "[data-horario-selecionado]",
        );
        if (horarioFoiClicado) {
          dateEl.disabled = true;
          timeEl.disabled = true;
          if (periodoCampos) periodoCampos.hidden = true;
          if (horarioSelecionado) {
            horarioSelecionado.hidden = false;
            horarioSelecionado.querySelector(
              "[data-horario-selecionado-texto]",
            ).textContent =
              dataSelecionada.split("-").reverse().join("/") +
              " às " +
              horaSelecionada;
          }
        }
        if (isEdit && btn.dataset.usuario_id && userSel)
          userSel.value = btn.dataset.usuario_id;
        var servicosSelecionados = (
          btn.dataset.servicos_ids ||
          btn.dataset.servico_id ||
          ""
        )
          .split(",")
          .map(function (id) {
            return id.trim();
          })
          .filter(Boolean);
        serviceInputs.forEach(function (input) {
          input.checked =
            isEdit && servicosSelecionados.indexOf(input.value) !== -1;
          input.addEventListener("change", atualizarResumo);
        });
        if (isEdit && btn.dataset.status && statusSel)
          statusSel.value = btn.dataset.status;
        var observacoesEl = popup.querySelector('[name="observacoes"]');
        if (observacoesEl) observacoesEl.value = btn.dataset.observacoes || "";
        timeEl.addEventListener("input", atualizarResumo);

        function atualizarResumo() {
          var selecionados = serviceInputs.filter(function (input) {
            return input.checked;
          });
          var resumo = popup.querySelector("[data-admin-agendamento-resumo]");
          if (!resumo) return;
          if (!selecionados.length) {
            resumo.hidden = true;
            return;
          }
          var minutos = selecionados.reduce(function (total, input) {
            return total + Number(input.dataset.duracao || 0);
          }, 0);
          var valor = selecionados.reduce(function (total, input) {
            return total + Number(input.dataset.preco || 0);
          }, 0);
          var partes = (timeEl.value || "00:00").split(":");
          var totalMinutos =
            Number(partes[0]) * 60 + Number(partes[1]) + minutos;
          var fim =
            String(Math.floor(totalMinutos / 60)).padStart(2, "0") +
            ":" +
            String(totalMinutos % 60).padStart(2, "0");
          resumo.hidden = false;
          resumo.querySelector("[data-admin-agendamento-lista]").textContent =
            selecionados
              .map(function (input) {
                return input.parentElement.querySelector("span").textContent;
              })
              .join(", ");
          resumo.querySelector("[data-admin-agendamento-total]").textContent =
            valor.toLocaleString("pt-BR", {
              style: "currency",
              currency: "BRL",
            });
          resumo.querySelector("[data-admin-agendamento-duracao]").textContent =
            minutos + " min";
          resumo.querySelector("[data-admin-agendamento-fim]").textContent =
            fim;
          popup.querySelector('[name="hora_fim"]').value = fim;
        }
        atualizarResumo();
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var payload = {
          action: isEdit ? "editar" : "criar",
          usuario_id: popup.querySelector('[name="usuario_id"]').value,
          servico_id:
            Array.prototype.slice
              .call(popup.querySelectorAll('[name="servicos_ids"]:checked'))
              .map(function (input) {
                return Number(input.value);
              })[0] || 0,
          servicos_ids: Array.prototype.slice
            .call(popup.querySelectorAll('[name="servicos_ids"]:checked'))
            .map(function (input) {
              return Number(input.value);
            }),
          data: popup.querySelector('[name="data"]').value,
          hora_inicio: popup.querySelector('[name="hora_inicio"]').value,
          hora_fim: popup.querySelector('[name="hora_fim"]').value,
          observacoes:
            (popup.querySelector('[name="observacoes"]') || {}).value || "",
          status: popup.querySelector('[name="status"]')
            ? popup.querySelector('[name="status"]').value
            : "pendente",
        };
        if (
          !payload.usuario_id ||
          !payload.servicos_ids.length ||
          !payload.data ||
          !payload.hora_inicio
        ) {
          Swal.showValidationMessage(
            "Preencha cliente, serviço, data e horário.",
          );
          return false;
        }
        if (isEdit) payload.id = btn.dataset.id;
        return fetch("../../api/admin/agendamentos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        }).then(function (r) {
          return r.json();
        });
      },
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: "success",
          title: isEdit ? "Atualizado!" : "Criado!",
          timer: 1500,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        SwalTP.fire({
          icon: "error",
          title: "Erro",
          text: (result.value && result.value.message) || "Erro ao salvar.",
        });
      }
    });
  });

  // ─── Criar a partir da célula da agenda ────────────────────────
  document.addEventListener("click", function (e) {
    var cell = e.target.closest(".agenda-cell--free");
    if (!cell) return;
    e.preventDefault();
    e.stopPropagation();
    var btn = document.createElement("button");
    btn.dataset.modal = "modal-agendamento";
    btn.dataset.data = cell.dataset.date;
    btn.dataset.hora = cell.dataset.time;
    btn.click();
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
      "Aguardamos você! 💈\nTio Preto Barbearia 🧔🏿";

    SwalTP.fire({
      title:
        '<span class="whatsapp-modal__icon"><i class="fa-brands fa-whatsapp"></i></span> Lembrete WhatsApp',
      html:
        '<div class="modal-field whatsapp-modal__recipient">' +
        '<label class="modal-label">Para: <strong>' +
        escapeHtml(nome) +
        "</strong>" +
        (tel
          ? " &nbsp;·&nbsp; +" + escapeHtml(tel)
          : ' <small class="whatsapp-modal__no-phone">(sem telefone)</small>') +
        "</label></div>" +
        '<div class="modal-field">' +
        '<label class="modal-label">Mensagem</label>' +
        '<textarea id="swal-whatsapp-msg" class="modal-textarea whatsapp-modal__message" rows="8">' +
        escapeHtml(msgDefault) +
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

  var dataLembrete = document.getElementById("agenda-lembrete-data");
  if (dataLembrete) {
    dataLembrete.addEventListener("change", function () {
      var botao = document.querySelector(".btn-agenda-whatsapp-dia");
      if (botao) botao.dataset.date = dataLembrete.value;
    });
  }

  document.addEventListener("click", function (e) {
    var botao = e.target.closest(".btn-agenda-whatsapp-dia");
    if (!botao) return;
    e.preventDefault();
    e.stopPropagation();

    var data = (dataLembrete && dataLembrete.value) || botao.dataset.date || "";
    if (!data) {
      SwalTP.fire({
        icon: "warning",
        title: "Selecione uma data",
        text: "Informe o dia para enviar os lembretes.",
      });
      return;
    }

    SwalTP.fire({
      title: "Enviar lembretes do dia?",
      text: "Todos os clientes com agendamento neste dia e telefone cadastrado receberão uma mensagem.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Enviar lembretes",
      cancelButtonText: "Voltar",
      preConfirm: function () {
        return fetch("../../api/admin/whatsapp.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "enviar_dia", data: data }),
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
      if (!result.value || !result.value.success) {
        SwalTP.fire({
          icon: "error",
          title: "Falha no envio",
          text:
            (result.value && result.value.message) ||
            "Não foi possível enviar os lembretes.",
        });
        return;
      }
      var dados = result.value.data || {};
      SwalTP.fire({
        icon: "success",
        title: "Lembretes processados",
        text:
          (dados.enviados || 0) +
          " enviado(s)." +
          (dados.sem_telefone
            ? " " + dados.sem_telefone + " sem telefone."
            : "") +
          (dados.falhas ? " " + dados.falhas + " falha(s)." : ""),
        timer: 3000,
        showConfirmButton: false,
      });
    });
  });
})();

// ─── Editor de horário de dia específico ────────────────────────────────────
(function () {
  "use strict";

  var diasNomes = [
    "",
    "Segunda-feira",
    "Terça-feira",
    "Quarta-feira",
    "Quinta-feira",
    "Sexta-feira",
    "Sábado",
    "Domingo",
  ];

  function formatarDataBR(iso) {
    var p = iso.split("-");
    return p[2] + "/" + p[1] + "/" + p[0];
  }

  function diaDaSemana(iso) {
    // date('N') em JS: getDay() retorna 0=Dom … 6=Sab; ISO: 1=Seg … 7=Dom
    var d = new Date(iso + "T12:00:00");
    var w = d.getDay(); // 0=Dom
    return w === 0 ? 7 : w; // converte para ISO
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-horario-dia");
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    var data = btn.dataset.date;
    var fechado = btn.dataset.fechado === "1";
    var abertura = btn.dataset.abertura || "08:00";
    var fechamento = btn.dataset.fechamento || "20:00";
    var temExcecao = btn.dataset.excecao === "1";
    var nomeDia = diasNomes[diaDaSemana(data)] || "";

    var htmlModal =
      '<div class="horario-dia-modal">' +
      '<p class="horario-dia-modal__data"><i class="fa-regular fa-calendar"></i> ' +
      "<strong>" +
      nomeDia +
      "</strong> — " +
      formatarDataBR(data) +
      "</p>" +
      (temExcecao
        ? '<p class="horario-dia-modal__aviso"><i class="fa-solid fa-circle-exclamation"></i> Exceção ativa para este dia específico.</p>'
        : "") +
      '<div class="horario-dia-modal__toggle">' +
      '<label class="horario-toggle">' +
      '<input type="checkbox" id="excecao-fechado"' +
      (fechado ? " checked" : "") +
      " />" +
      '<span class="horario-toggle__track"></span>' +
      '<span class="horario-toggle__label" id="excecao-fechado-label">' +
      (fechado ? "Fechado" : "Aberto") +
      "</span>" +
      "</label>" +
      "</div>" +
      '<div class="horario-dia-modal__horas" id="excecao-horas"' +
      (fechado ? ' style="display:none"' : "") +
      ">" +
      '<label>Abertura <input type="time" id="excecao-abertura" value="' +
      abertura +
      '" step="1800" class="horario-input" /></label>' +
      '<label>Fechamento <input type="time" id="excecao-fechamento" value="' +
      fechamento +
      '" step="1800" class="horario-input" /></label>' +
      "</div>" +
      (temExcecao
        ? '<p class="horario-dia-modal__restaurar"><button type="button" id="btn-restaurar-padrao" class="btn-secondary" style="font-size:.8rem;padding:6px 12px"><i class="fa-solid fa-rotate-left"></i> Restaurar padrão da semana</button></p>'
        : "") +
      "</div>";

    window.SwalTP.fire({
      title: '<i class="fa-regular fa-clock"></i> Horário do dia',
      html: htmlModal,
      showCancelButton: true,
      confirmButtonText:
        '<i class="fa-solid fa-floppy-disk"></i> Salvar este dia',
      cancelButtonText: "Cancelar",
      width: "440px",
      didOpen: function (popup) {
        var checkFechado = popup.querySelector("#excecao-fechado");
        var labelFechado = popup.querySelector("#excecao-fechado-label");
        var divHoras = popup.querySelector("#excecao-horas");

        checkFechado.addEventListener("change", function () {
          if (checkFechado.checked) {
            divHoras.style.display = "none";
            labelFechado.textContent = "Fechado";
          } else {
            divHoras.style.display = "";
            labelFechado.textContent = "Aberto";
          }
        });

        var btnRestore = popup.querySelector("#btn-restaurar-padrao");
        if (btnRestore) {
          btnRestore.addEventListener("click", function () {
            fetch("../../api/admin/horarios.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ action: "excecao_remover", data: data }),
            })
              .then(function (r) {
                return r.json();
              })
              .catch(function () {
                return { success: false, message: "Erro de rede." };
              })
              .then(function (res) {
                window.SwalTP.close();
                if (res.success) {
                  window.SwalTP.fire({
                    icon: "success",
                    title: "Padrão restaurado!",
                    timer: 1800,
                    showConfirmButton: false,
                  }).then(function () {
                    location.reload();
                  });
                } else {
                  window.SwalTP.fire({
                    icon: "error",
                    title: "Erro",
                    text: res.message,
                  });
                }
              });
          });
        }
      },
      preConfirm: function () {
        var popup = document.querySelector(".swal2-popup");
        var isFechado = popup.querySelector("#excecao-fechado").checked;
        var ab = popup.querySelector("#excecao-abertura").value;
        var fec = popup.querySelector("#excecao-fechamento").value;

        if (!isFechado && (!ab || !fec)) {
          window.Swal.showValidationMessage(
            "Informe os horários de abertura e fechamento.",
          );
          return false;
        }
        if (!isFechado && ab >= fec) {
          window.Swal.showValidationMessage(
            "A abertura deve ser anterior ao fechamento.",
          );
          return false;
        }
        return fetch("../../api/admin/horarios.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "excecao",
            data: data,
            fechado: isFechado,
            abertura: ab,
            fechamento: fec,
          }),
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
        window.SwalTP.fire({
          icon: "success",
          title: "Salvo!",
          timer: 1800,
          showConfirmButton: false,
        }).then(function () {
          location.reload();
        });
      } else {
        window.SwalTP.fire({
          icon: "error",
          title: "Erro",
          text:
            (result.value && result.value.message) ||
            "Não foi possível salvar.",
        });
      }
    });
  });
})();

// Alterna entre a grade semanal e a lista de agendamentos.
(function () {
  function escapeHtml(value) {
    var element = document.createElement("div");
    element.textContent = value || "";
    return element.innerHTML;
  }

  ("use strict");

  var agendaButton = document.getElementById("btn-agenda");
  var listButton = document.getElementById("btn-lista");
  var agendaView = document.getElementById("view-agenda");
  var listView = document.getElementById("view-lista");

  if (!agendaButton || !listButton || !agendaView || !listView) return;

  agendaButton.addEventListener("click", function () {
    agendaView.hidden = false;
    listView.hidden = true;
    agendaButton.classList.add("active");
    listButton.classList.remove("active");
  });

  listButton.addEventListener("click", function () {
    listView.hidden = false;
    agendaView.hidden = true;
    listButton.classList.add("active");
    agendaButton.classList.remove("active");
  });
})();

// ── Navegação por data: pula para a semana da data escolhida ──
(function () {
  var input = document.querySelector(".agenda-goto-input");
  if (!input) return;
  input.addEventListener("change", function () {
    if (input.value) window.location.href = "?data=" + input.value;
  });
})();

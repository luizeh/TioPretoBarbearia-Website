// horarios.js — Gerencia os horários de funcionamento no painel admin.
(function () {
  "use strict";

  // ── Toggle Fechado/Aberto ─────────────────────────────────────────
  document.querySelectorAll(".horario-fechado-check").forEach(function (check) {
    check.addEventListener("change", function () {
      var row = check.closest(".horario-row");
      var abertura = row.querySelector(".horario-abertura");
      var fechamento = row.querySelector(".horario-fechamento");
      var label = row.querySelector(".horario-toggle__label");

      if (check.checked) {
        abertura.disabled = true;
        fechamento.disabled = true;
        row.classList.add("horario-row--fechado");
        label.textContent = "Fechado";
      } else {
        abertura.disabled = false;
        fechamento.disabled = false;
        row.classList.remove("horario-row--fechado");
        label.textContent = "Aberto";
      }
    });
  });

  // ── Salvar horário individual ─────────────────────────────────────
  document.querySelectorAll(".horario-salvar").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var dia = Number(btn.dataset.dia);
      var row = btn.closest(".horario-row");

      var fechado = row.querySelector(".horario-fechado-check").checked;
      var abertura = row.querySelector(".horario-abertura").value || "08:00";
      var fechamento =
        row.querySelector(".horario-fechamento").value || "20:00";

      if (!fechado && abertura >= fechamento) {
        window.SwalTP.fire({
          icon: "warning",
          title: "Horário inválido",
          text: "A abertura deve ser anterior ao fechamento.",
        });
        return;
      }

      var payload = {
        dia_semana: dia,
        abertura: abertura,
        fechamento: fechamento,
        fechado: fechado,
      };

      fetch("../../api/admin/horarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then(function (r) {
          return r.json();
        })
        .catch(function () {
          return { success: false, message: "Erro de rede." };
        })
        .then(function (data) {
          if (data.success) {
            window.SwalTP.fire({
              icon: "success",
              title: "Salvo!",
              timer: 1800,
              showConfirmButton: false,
            });
          } else {
            window.SwalTP.fire({
              icon: "error",
              title: "Erro",
              text: data.message || "Não foi possível salvar.",
            });
          }
        });
    });
  });
})();

// ── Bloqueios recorrentes ─────────────────────────────────────────────────────
(function () {
  "use strict";

  var nomes = [
    "",
    "Segunda-feira",
    "Terça-feira",
    "Quarta-feira",
    "Quinta-feira",
    "Sexta-feira",
    "Sábado",
    "Domingo",
  ];

  function post(payload) {
    return fetch("../../api/admin/horarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    })
      .then(function (r) {
        return r.json();
      })
      .catch(function () {
        return { success: false, message: "Erro de rede." };
      });
  }

  function adicionarLinhaTabela(bloqueio) {
    var tbody = document.getElementById("tbl-bloqueios-body");
    var empty = document.getElementById("bloqueio-empty");
    if (empty) empty.remove();

    var tr = document.createElement("tr");
    tr.dataset.bloqueioId = bloqueio.id;
    tr.innerHTML =
      "<td>" +
      (bloqueio.dia_semana
        ? nomes[bloqueio.dia_semana] || "—"
        : "<em>Todos os dias</em>") +
      "</td>" +
      "<td>" +
      bloqueio.hora_inicio.substring(0, 5) +
      "</td>" +
      "<td>" +
      bloqueio.hora_fim.substring(0, 5) +
      "</td>" +
      "<td>" +
      (bloqueio.descricao || "—") +
      "</td>" +
      '<td><button type="button" class="btn-action btn-action--delete btn-bloqueio-excluir" data-id="' +
      bloqueio.id +
      '" title="Remover"><i class="fa-solid fa-trash"></i></button></td>';
    tbody.appendChild(tr);
  }

  // ── Adicionar bloqueio ──────────────────────────────────────────
  var btnAdd = document.getElementById("btn-bloqueio-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", function () {
      var dia = document.getElementById("bloqueio-dia").value;
      var inicio = document.getElementById("bloqueio-inicio").value;
      var fim = document.getElementById("bloqueio-fim").value;
      var desc = document.getElementById("bloqueio-desc").value.trim();

      if (!inicio || !fim) {
        window.SwalTP.fire({
          icon: "warning",
          title: "Preencha os horários",
          text: "Início e fim são obrigatórios.",
        });
        return;
      }
      if (inicio >= fim) {
        window.SwalTP.fire({
          icon: "warning",
          title: "Horário inválido",
          text: "Início deve ser anterior ao fim.",
        });
        return;
      }

      post({
        action: "bloqueio_criar",
        dia_semana: dia === "" ? null : Number(dia),
        hora_inicio: inicio,
        hora_fim: fim,
        descricao: desc || null,
      }).then(function (data) {
        if (data.success) {
          adicionarLinhaTabela({
            id: data.data.id,
            dia_semana: dia === "" ? null : Number(dia),
            hora_inicio: inicio + ":00",
            hora_fim: fim + ":00",
            descricao: desc || null,
          });
          document.getElementById("bloqueio-inicio").value = "";
          document.getElementById("bloqueio-fim").value = "";
          document.getElementById("bloqueio-desc").value = "";
          document.getElementById("bloqueio-dia").value = "";
          window.SwalTP.fire({
            icon: "success",
            title: "Bloqueio criado!",
            timer: 1500,
            showConfirmButton: false,
          });
        } else {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: data.message,
          });
        }
      });
    });
  }

  // ── Excluir bloqueio (delegado) ────────────────────────────────
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-bloqueio-excluir");
    if (!btn) return;
    var id = Number(btn.dataset.id);
    window.SwalTP.fire({
      icon: "question",
      title: "Remover bloqueio?",
      showCancelButton: true,
      confirmButtonText: "Remover",
      cancelButtonText: "Cancelar",
    }).then(function (result) {
      if (!result.isConfirmed) return;
      post({ action: "bloqueio_excluir", id: id }).then(function (data) {
        if (data.success) {
          var row = document.querySelector('[data-bloqueio-id="' + id + '"]');
          if (row) row.remove();
          if (!document.querySelector("#tbl-bloqueios-body tr")) {
            var tbody = document.getElementById("tbl-bloqueios-body");
            tbody.innerHTML =
              '<tr id="bloqueio-empty"><td colspan="5" class="table-empty-cell">Nenhum bloqueio configurado.</td></tr>';
          }
          window.SwalTP.fire({
            icon: "success",
            title: "Removido!",
            timer: 1200,
            showConfirmButton: false,
          });
        } else {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro",
            text: data.message,
          });
        }
      });
    });
  });
})();

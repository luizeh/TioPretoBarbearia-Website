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
        action: "salvar",
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
// ── Salvar Todos ──────────────────────────────────────────────────────────────────
(function () {
  "use strict";

  var btnSalvarTodos = document.getElementById("btn-horarios-salvar-todos");
  if (!btnSalvarTodos) return;

  btnSalvarTodos.addEventListener("click", function () {
    var rows = document.querySelectorAll(".horario-row");
    var dias = [];
    var invalidos = [];

    rows.forEach(function (row) {
      var dia = Number(row.dataset.dia);
      var fechado = row.querySelector(".horario-fechado-check").checked;
      var abertura = row.querySelector(".horario-abertura").value || "08:00";
      var fechamento =
        row.querySelector(".horario-fechamento").value || "20:00";

      if (!fechado && abertura >= fechamento) {
        invalidos.push(dia);
        return;
      }
      dias.push({
        dia_semana: dia,
        abertura: abertura,
        fechamento: fechamento,
        fechado: fechado,
      });
    });

    if (invalidos.length > 0 && dias.length === 0) {
      window.SwalTP.fire({
        icon: "warning",
        title: "Horários inválidos",
        text: "Corrija os horários antes de salvar.",
      });
      return;
    }

    fetch("../../api/admin/horarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "salvar_todos", dias: dias }),
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
            title: "Todos os horários salvos!",
            timer: 1800,
            showConfirmButton: false,
          });
        } else {
          window.SwalTP.fire({
            icon: "error",
            title: "Erro ao salvar",
            text: data.message || "Não foi possível salvar.",
          });
        }
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

  var abrev = ["", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"];

  function labelDiaBloqueio(dia, exc) {
    if (dia) return nomes[Number(dia)] || "—";
    if (exc && exc.length) {
      return '<em>Todos os dias</em> <small class="swal-text-muted">(exceto ' +
        exc.map(function (d) { return abrev[d] || d; }).join(", ") + ")</small>";
    }
    return "<em>Todos os dias</em>";
  }

  function adicionarLinhaTabela(bloqueio) {
    var tbody = document.getElementById("tbl-bloqueios-body");
    var empty = document.getElementById("bloqueio-empty");
    if (empty) empty.remove();

    var tr = document.createElement("tr");
    var hi = bloqueio.hora_inicio.substring(0, 5);
    var hf = bloqueio.hora_fim.substring(0, 5);
    var exc = bloqueio.dias_excecao || [];
    tr.dataset.bloqueioId = bloqueio.id;
    tr.dataset.dia = bloqueio.dia_semana ? String(bloqueio.dia_semana) : "";
    tr.dataset.excecao = exc.join(",");
    tr.dataset.inicio = hi;
    tr.dataset.fim = hf;
    tr.dataset.descricao = bloqueio.descricao || "";
    tr.innerHTML =
      "<td>" +
      labelDiaBloqueio(bloqueio.dia_semana, exc) +
      "</td>" +
      "<td>" +
      hi +
      "</td>" +
      "<td>" +
      hf +
      "</td>" +
      "<td>" +
      (bloqueio.descricao || "—") +
      "</td>" +
      '<td><button type="button" class="btn-action btn-action--edit btn-bloqueio-editar" data-id="' +
      bloqueio.id +
      '" title="Editar"><i class="fa-solid fa-pen"></i></button> ' +
      '<button type="button" class="btn-action btn-action--delete btn-bloqueio-excluir" data-id="' +
      bloqueio.id +
      '" title="Remover"><i class="fa-solid fa-trash"></i></button></td>';
    tbody.appendChild(tr);
  }

  // ── Exceção de dias (só quando "Todos os dias") ─────────────────
  var selDiaBloqueio = document.getElementById("bloqueio-dia");
  var excecaoWrap = document.getElementById("bloqueio-excecao-wrap");
  function toggleExcecaoWrap() {
    if (excecaoWrap) excecaoWrap.hidden = selDiaBloqueio.value !== "";
  }
  function getExcecaoDias() {
    return Array.prototype.slice
      .call(document.querySelectorAll(".bloqueio-excecao-dia:checked"))
      .map(function (c) { return Number(c.value); });
  }
  function clearExcecaoDias() {
    document.querySelectorAll(".bloqueio-excecao-dia").forEach(function (c) {
      c.checked = false;
    });
  }
  if (selDiaBloqueio) {
    selDiaBloqueio.addEventListener("change", toggleExcecaoWrap);
    toggleExcecaoWrap();
  }

  // ── Adicionar bloqueio ──────────────────────────────────────────
  var btnAdd = document.getElementById("btn-bloqueio-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", function () {
      var dia = document.getElementById("bloqueio-dia").value;
      var inicio = document.getElementById("bloqueio-inicio").value;
      var fim = document.getElementById("bloqueio-fim").value;
      var desc = document.getElementById("bloqueio-desc").value.trim();
      var diasExcecao = dia === "" ? getExcecaoDias() : [];

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
        dias_excecao: diasExcecao,
        hora_inicio: inicio,
        hora_fim: fim,
        descricao: desc || null,
      }).then(function (data) {
        if (data.success) {
          adicionarLinhaTabela({
            id: data.data.id,
            dia_semana: dia === "" ? null : Number(dia),
            dias_excecao: diasExcecao,
            hora_inicio: inicio + ":00",
            hora_fim: fim + ":00",
            descricao: desc || null,
          });
          document.getElementById("bloqueio-inicio").value = "";
          document.getElementById("bloqueio-fim").value = "";
          document.getElementById("bloqueio-desc").value = "";
          document.getElementById("bloqueio-dia").value = "";
          clearExcecaoDias();
          toggleExcecaoWrap();
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
    window.SwalTP.confirmarExclusao({
      title: "Remover bloqueio?",
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
// ── Editar bloqueios recorrentes (modal) + Bloqueios por período ───────────────
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

  function escapeHtml(v) {
    var d = document.createElement("div");
    d.textContent = v == null ? "" : v;
    return d.innerHTML;
  }

  function fmtData(iso) {
    var p = String(iso).split("-");
    return p[2] + "/" + p[1] + "/" + p[0];
  }

  var abrev = ["", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"];

  function labelDiaBloqueio(dia, exc) {
    if (dia) return nomes[Number(dia)] || "—";
    if (exc && exc.length) {
      return '<em>Todos os dias</em> <small class="swal-text-muted">(exceto ' +
        exc.map(function (d) { return abrev[d] || d; }).join(", ") + ")</small>";
    }
    return "<em>Todos os dias</em>";
  }

  // ═══ Editar bloqueio recorrente (modal) ═══
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-bloqueio-editar");
    if (!btn) return;
    var tr = btn.closest("[data-bloqueio-id]");
    var id = Number(tr.dataset.bloqueioId);
    var dia = tr.dataset.dia || "";
    var inicio = tr.dataset.inicio || "";
    var fim = tr.dataset.fim || "";
    var desc = tr.dataset.descricao || "";
    var exc = (tr.dataset.excecao || "").split(",").filter(Boolean).map(Number);

    var opts = '<option value="">Todos os dias</option>';
    for (var d = 1; d <= 7; d++) {
      opts +=
        '<option value="' + d + '"' +
        (String(d) === String(dia) ? " selected" : "") +
        ">" + nomes[d] + "</option>";
    }
    var chips = "";
    for (var x = 1; x <= 7; x++) {
      chips +=
        '<label class="swal-chip"><input type="checkbox" class="eb-exc" value="' +
        x + '"> ' + abrev[x] + "</label>";
    }

    var campo = 'class="swal-form-label"';
    var input = 'class="horario-input"';

    window.SwalTP.fire({
      title: "Editar bloqueio",
      html:
        '<div class="swal-form-grid">' +
        "<label " + campo + ">Dia da semana<select id=\"eb-dia\" " + input + ">" + opts + "</select></label>" +
        "<label " + campo + ">Início<input type=\"time\" id=\"eb-inicio\" step=\"1800\" " + input + "></label>" +
        "<label " + campo + ">Fim<input type=\"time\" id=\"eb-fim\" step=\"1800\" " + input + "></label>" +
        "<label " + campo + ">Descrição<input type=\"text\" id=\"eb-desc\" maxlength=\"100\" " + input + "></label>" +
        "<div id=\"eb-excecao-wrap\"><span " + campo + ">Exceto nos dias</span><div class=\"swal-chips\">" + chips + "</div></div>" +
        "</div>",
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      didOpen: function () {
        document.getElementById("eb-inicio").value = inicio;
        document.getElementById("eb-fim").value = fim;
        document.getElementById("eb-desc").value = desc;
        var sel = document.getElementById("eb-dia");
        var wrap = document.getElementById("eb-excecao-wrap");
        function tog() { wrap.style.display = sel.value === "" ? "" : "none"; }
        sel.addEventListener("change", tog);
        document.querySelectorAll(".eb-exc").forEach(function (c) {
          c.checked = exc.indexOf(Number(c.value)) !== -1;
        });
        tog();
      },
      preConfirm: function () {
        var di = document.getElementById("eb-dia").value;
        var hi = document.getElementById("eb-inicio").value;
        var hf = document.getElementById("eb-fim").value;
        var de = document.getElementById("eb-desc").value.trim();
        var exd = di === ""
          ? Array.prototype.slice.call(document.querySelectorAll(".eb-exc:checked")).map(function (c) { return Number(c.value); })
          : [];
        if (!hi || !hf) {
          window.Swal.showValidationMessage("Início e fim são obrigatórios.");
          return false;
        }
        if (hi >= hf) {
          window.Swal.showValidationMessage("Início deve ser anterior ao fim.");
          return false;
        }
        return post({
          action: "bloqueio_editar",
          id: id,
          dia_semana: di === "" ? null : Number(di),
          dias_excecao: exd,
          hora_inicio: hi,
          hora_fim: hf,
          descricao: de || null,
        }).then(function (r) {
          if (!r.success) {
            window.Swal.showValidationMessage(r.message || "Erro ao salvar.");
            return false;
          }
          return { di: di, hi: hi, hf: hf, de: de, exc: exd };
        });
      },
    }).then(function (res) {
      if (!res.isConfirmed || !res.value) return;
      var v = res.value;
      tr.dataset.dia = v.di;
      tr.dataset.excecao = (v.exc || []).join(",");
      tr.dataset.inicio = v.hi;
      tr.dataset.fim = v.hf;
      tr.dataset.descricao = v.de;
      var tds = tr.querySelectorAll("td");
      tds[0].innerHTML = labelDiaBloqueio(v.di, v.exc);
      tds[1].textContent = v.hi;
      tds[2].textContent = v.hf;
      tds[3].textContent = v.de || "—";
      window.SwalTP.fire({ icon: "success", title: "Bloqueio atualizado!", timer: 1400, showConfirmButton: false });
    });
  });

  // ═══ Bloqueios por período ═══
  var elInicio = document.getElementById("periodo-inicio");
  if (!elInicio) return;

  var elFim = document.getElementById("periodo-fim");
  var elDiaInteiro = document.getElementById("periodo-dia-inteiro");
  var elHoraInicio = document.getElementById("periodo-hora-inicio");
  var elHoraFim = document.getElementById("periodo-hora-fim");
  var elDesc = document.getElementById("periodo-desc");
  var elId = document.getElementById("periodo-id");
  var btnAdd = document.getElementById("btn-periodo-add");
  var btnCancelar = document.getElementById("btn-periodo-cancelar");
  var tituloForm = document.getElementById("periodo-form-titulo");
  var btnLabel = document.getElementById("periodo-btn-label");
  var horasCampos = document.querySelectorAll(".periodo-horas");

  function toggleHoras() {
    var mostra = !elDiaInteiro.checked;
    horasCampos.forEach(function (c) {
      c.hidden = !mostra;
    });
  }
  elDiaInteiro.addEventListener("change", toggleHoras);
  toggleHoras();

  function resetForm() {
    elId.value = "";
    elInicio.value = "";
    elFim.value = "";
    elDiaInteiro.checked = true;
    elHoraInicio.value = "";
    elHoraFim.value = "";
    elDesc.value = "";
    toggleHoras();
    tituloForm.textContent = "Novo Período";
    btnLabel.textContent = "Criar Período";
    btnCancelar.hidden = true;
  }
  btnCancelar.addEventListener("click", resetForm);

  function preencherLinha(tr, p) {
    tr.dataset.periodoId = p.id;
    tr.dataset.inicio = p.data_inicio;
    tr.dataset.fim = p.data_fim;
    tr.dataset.diaInteiro = p.dia_inteiro ? "1" : "0";
    tr.dataset.horaInicio = p.hora_inicio || "";
    tr.dataset.horaFim = p.hora_fim || "";
    tr.dataset.descricao = p.descricao || "";
    var intervalo =
      p.data_inicio === p.data_fim
        ? fmtData(p.data_inicio)
        : fmtData(p.data_inicio) + " → " + fmtData(p.data_fim);
    var bloqueio = p.dia_inteiro
      ? "<em>Dia inteiro</em>"
      : escapeHtml(p.hora_inicio + "–" + p.hora_fim);
    tr.innerHTML =
      "<td>" + intervalo + "</td>" +
      "<td>" + bloqueio + "</td>" +
      "<td>" + (p.descricao ? escapeHtml(p.descricao) : "—") + "</td>" +
      '<td><button type="button" class="btn-action btn-action--edit btn-periodo-editar" data-id="' +
      p.id +
      '" title="Editar"><i class="fa-solid fa-pen"></i></button> ' +
      '<button type="button" class="btn-action btn-action--delete btn-periodo-excluir" data-id="' +
      p.id +
      '" title="Remover"><i class="fa-solid fa-trash"></i></button></td>';
  }

  function coletar() {
    var diaInteiro = elDiaInteiro.checked;
    var di = elInicio.value;
    var df = elFim.value;
    if (!di || !df) {
      window.SwalTP.fire({ icon: "warning", title: "Datas obrigatórias", text: "Informe a data inicial e final." });
      return null;
    }
    if (df < di) {
      window.SwalTP.fire({ icon: "warning", title: "Datas inválidas", text: "A data final deve ser igual ou posterior à inicial." });
      return null;
    }
    var hi = null,
      hf = null;
    if (!diaInteiro) {
      hi = elHoraInicio.value;
      hf = elHoraFim.value;
      if (!hi || !hf) {
        window.SwalTP.fire({ icon: "warning", title: "Horários obrigatórios", text: 'Informe início e fim, ou marque "dia inteiro".' });
        return null;
      }
      if (hi >= hf) {
        window.SwalTP.fire({ icon: "warning", title: "Horário inválido", text: "Início deve ser anterior ao fim." });
        return null;
      }
    }
    return {
      data_inicio: di,
      data_fim: df,
      dia_inteiro: diaInteiro,
      hora_inicio: hi,
      hora_fim: hf,
      descricao: elDesc.value.trim() || null,
    };
  }

  btnAdd.addEventListener("click", function () {
    var dados = coletar();
    if (!dados) return;
    var editando = elId.value !== "";
    var payload = Object.assign({ action: editando ? "periodo_editar" : "periodo_criar" }, dados);
    if (editando) payload.id = Number(elId.value);
    post(payload).then(function (data) {
      if (!data.success) {
        window.SwalTP.fire({ icon: "error", title: "Erro", text: data.message });
        return;
      }
      var pObj = {
        id: editando ? Number(elId.value) : data.data.id,
        data_inicio: dados.data_inicio,
        data_fim: dados.data_fim,
        dia_inteiro: dados.dia_inteiro,
        hora_inicio: dados.hora_inicio,
        hora_fim: dados.hora_fim,
        descricao: dados.descricao,
      };
      var tbody = document.getElementById("tbl-periodos-body");
      var empty = document.getElementById("periodo-empty");
      if (empty) empty.remove();
      var tr = editando
        ? document.querySelector('[data-periodo-id="' + pObj.id + '"]')
        : document.createElement("tr");
      preencherLinha(tr, pObj);
      if (!editando) tbody.appendChild(tr);
      resetForm();
      window.SwalTP.fire({
        icon: "success",
        title: editando ? "Período atualizado!" : "Período criado!",
        timer: 1500,
        showConfirmButton: false,
      });
    });
  });

  // Editar (preenche o formulário)
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-periodo-editar");
    if (!btn) return;
    var tr = btn.closest("[data-periodo-id]");
    elId.value = tr.dataset.periodoId;
    elInicio.value = tr.dataset.inicio;
    elFim.value = tr.dataset.fim;
    elDiaInteiro.checked = tr.dataset.diaInteiro === "1";
    elHoraInicio.value = tr.dataset.horaInicio || "";
    elHoraFim.value = tr.dataset.horaFim || "";
    elDesc.value = tr.dataset.descricao || "";
    toggleHoras();
    tituloForm.textContent = "Editar Período";
    btnLabel.textContent = "Salvar Período";
    btnCancelar.hidden = false;
    document.getElementById("card-periodos").scrollIntoView({ behavior: "smooth", block: "start" });
  });

  // Excluir
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-periodo-excluir");
    if (!btn) return;
    var id = Number(btn.dataset.id);
    window.SwalTP.confirmarExclusao({
      title: "Remover período?",
      confirmButtonText: "Remover",
      cancelButtonText: "Cancelar",
    }).then(function (result) {
      if (!result.isConfirmed) return;
      post({ action: "periodo_excluir", id: id }).then(function (data) {
        if (!data.success) {
          window.SwalTP.fire({ icon: "error", title: "Erro", text: data.message });
          return;
        }
        var row = document.querySelector('[data-periodo-id="' + id + '"]');
        if (row) row.remove();
        if (!document.querySelector("#tbl-periodos-body tr")) {
          document.getElementById("tbl-periodos-body").innerHTML =
            '<tr id="periodo-empty"><td colspan="4" class="table-empty-cell">Nenhum período configurado.</td></tr>';
        }
        if (elId.value === String(id)) resetForm();
        window.SwalTP.fire({ icon: "success", title: "Removido!", timer: 1200, showConfirmButton: false });
      });
    });
  });
})();

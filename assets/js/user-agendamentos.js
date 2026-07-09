// user-agendamentos.js — Novo agendamento + cancelar + slots calendário

(function () {
  var BASE = window.API_BASE || '../../api/';

  // ── Calendário de slots ────────────────────────────────────────
  var datePicker = document.getElementById('agenda-date-picker');
  if (datePicker) {
    datePicker.addEventListener('change', function () { carregarSlots(this.value); });
    carregarSlots(datePicker.value);
  }

  function carregarSlots(data) {
    var container = document.getElementById('agenda-slots-container');
    if (!container) return;
    container.innerHTML = '<p class="agenda-slots-loading"><i class="fa-solid fa-spinner fa-spin"></i> Carregando...</p>';
    fetch(BASE + 'user/slots.php?data=' + encodeURIComponent(data))
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.success) { container.innerHTML = '<p class="agenda-slots-loading">Erro ao carregar horários.</p>'; return; }
        renderSlots(container, res.data, data);
      })
      .catch(function () { container.innerHTML = '<p class="agenda-slots-loading">Sem conexão.</p>'; });
  }

  function renderSlots(container, slots, data) {
    if (!slots || !slots.length) { container.innerHTML = '<p class="agenda-slots-loading">Sem horários neste dia.</p>'; return; }
    container.innerHTML = slots.map(function (slot) {
      if (slot.status === 'livre') {
        return '<button class="agenda-slot agenda-slot--livre" data-slot-data="' + data + '" data-slot-hora="' + slot.hora + '">'
          + '<span class="agenda-slot__hora">' + slot.hora + '</span>'
          + '<span class="agenda-slot__label">Disponível</span></button>';
      }
      if (slot.status === 'meu') {
        var srv = slot.servico || 'Meu horário';
        var lbl = srv.length > 13 ? srv.substring(0, 11) + '…' : srv;
        return '<div class="agenda-slot agenda-slot--meu">'
          + '<span class="agenda-slot__hora">' + slot.hora + '</span>'
          + '<span class="agenda-slot__label">' + lbl + '</span>'
          + (slot.id ? '<span class="agenda-slot__label" style="color:#ef4444;cursor:pointer;text-decoration:underline;" data-cancelar-slot="' + slot.id + '" data-servico="' + (slot.servico || '') + '">cancelar</span>' : '')
          + '</div>';
      }
      return '<div class="agenda-slot agenda-slot--ocupado"><span class="agenda-slot__hora">' + slot.hora + '</span><span class="agenda-slot__label">Ocupado</span></div>';
    }).join('');
  }

  // Clique em slot disponível → modal com data/hora pré-preenchidos
  document.addEventListener('click', function (e) {
    var slotBtn = e.target.closest('[data-slot-hora]');
    if (slotBtn) { e.preventDefault(); abrirModalNovoAgendamento(slotBtn.dataset.slotData, slotBtn.dataset.slotHora); }
    var cancelSlot = e.target.closest('[data-cancelar-slot]');
    if (cancelSlot) { e.preventDefault(); dispararCancelar(cancelSlot.dataset.cancelarSlot, cancelSlot.dataset.servico); }
  });

  // ─── Novo Agendamento ─────────────────────────────────────────
  var btnNovo = document.getElementById('btn-novo-agendamento');
  if (btnNovo) btnNovo.addEventListener('click', function () { abrirModalNovoAgendamento(); });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-modal="modal-novo-agendamento"]');
    if (btn) { e.preventDefault(); abrirModalNovoAgendamento(); }
  });

  function abrirModalNovoAgendamento(preDate, preHora) {
    var overlay = document.getElementById('modal-novo-agendamento');
    if (!overlay) return;
    SwalTP.fire({
      title: 'Novo Agendamento',
      html: overlay.querySelector('.modal-body').innerHTML,
      showCancelButton: true,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      width: '520px',
      didOpen: function (popup) {
        var dateEl = popup.querySelector('[name="data"]');
        if (dateEl) { dateEl.min = new Date().toISOString().slice(0, 10); if (preDate) dateEl.value = preDate; }
        var selSvc = popup.querySelector('[name="servico_id"]');
        var inIni  = popup.querySelector('[name="hora_inicio"]');
        var inFim  = popup.querySelector('[name="hora_fim"]');
        var info   = popup.querySelector('#novoag-duracao-info');
        var fimEl  = popup.querySelector('#novoag-hora-fim-display');
        if (inIni && preHora) inIni.value = preHora;
        function calcFim() {
          if (!selSvc || !inIni || !inFim) return;
          var opt = selSvc.options[selSvc.selectedIndex];
          var dur = parseInt(opt ? (opt.dataset.duracao || '0') : '0', 10);
          var val = inIni.value;
          if (!val || !dur) { if (info) info.style.display = 'none'; return; }
          var p = val.split(':'), total = parseInt(p[0], 10) * 60 + parseInt(p[1], 10) + dur;
          var hF = String(Math.floor(total / 60)).padStart(2, '0');
          var mF = String(total % 60).padStart(2, '0');
          inFim.value = hF + ':' + mF;
          if (fimEl) fimEl.textContent = hF + ':' + mF;
          if (info)  info.style.display = '';
        }
        if (selSvc) selSvc.addEventListener('change', calcFim);
        if (inIni)  inIni.addEventListener('change', calcFim);
        if (preHora) calcFim();
      },
      preConfirm: function () {
        var p = document.querySelector('.swal2-popup');
        var svc  = p.querySelector('[name="servico_id"]').value;
        var data = p.querySelector('[name="data"]').value;
        var ini  = p.querySelector('[name="hora_inicio"]').value;
        var fim  = p.querySelector('[name="hora_fim"]').value || ini;
        if (!svc)  { Swal.showValidationMessage('Selecione um serviço.');       return false; }
        if (!data) { Swal.showValidationMessage('Informe a data.');              return false; }
        if (!ini)  { Swal.showValidationMessage('Informe o horário de início.'); return false; }
        return fetch(BASE + 'user/agendamentos.php', {
          method: 'POST', headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'criar', servico_id: svc, data: data, hora_inicio: ini, hora_fim: fim })
        }).then(function (r) { return r.json(); });
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({ icon: 'success', title: 'Agendado!', text: 'Confirmação enviada via WhatsApp!', timer: 2800, showConfirmButton: false })
          .then(function () { location.reload(); });
      } else {
        SwalTP.fire({ icon: 'error', title: 'Erro', text: (result.value && result.value.message) || 'Não foi possível criar.' });
      }
    });
  }

  // ─── Cancelar Agendamento ──────────────────────────────────────
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-cancelar');
    if (!btn) return;
    e.preventDefault();
    dispararCancelar(btn.dataset.cancelarId, btn.dataset.servico);
  });

  function dispararCancelar(id, servico) {
    SwalTP.fire({
      title: 'Cancelar Agendamento',
      html: 'Cancelar <strong>' + (servico || 'agendamento') + '</strong>?<br><small style="color:#888;">Esta ação não pode ser desfeita.</small>',
      showCancelButton: true, confirmButtonText: 'Sim, cancelar', cancelButtonText: 'Voltar',
      customClass: { confirmButton: 'swal-tp__btn swal-tp__btn--danger', cancelButton: 'swal-tp__btn swal-tp__btn--cancel' },
      preConfirm: function () {
        return fetch(BASE + 'user/agendamentos.php', {
          method: 'POST', headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'cancelar', id: id })
        }).then(function (r) { return r.json(); });
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({ icon: 'success', title: 'Cancelado!', timer: 1500, showConfirmButton: false })
          .then(function () { location.reload(); });
      } else {
        SwalTP.fire({ icon: 'error', title: 'Erro', text: (result.value && result.value.message) || 'Não foi possível cancelar.' });
      }
    });
  }
})();

  // --- Novo Agendamento ---
  var btnNovo = document.getElementById('btn-novo-agendamento');
  if (btnNovo) btnNovo.addEventListener('click', function () { abrirModalNovoAgendamento(); });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-modal="modal-novo-agendamento"]');
    if (btn) { e.preventDefault(); abrirModalNovoAgendamento(); }
  });

  function abrirModalNovoAgendamento() {
    var overlay = document.getElementById('modal-novo-agendamento');
    if (!overlay) return;
    SwalTP.fire({
      title: 'Novo Agendamento',
      html: overlay.querySelector('.modal-body').innerHTML,
      showCancelButton: true,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      width: '520px',
      didOpen: function (popup) {
        var dateEl = popup.querySelector('[name="data"]');
        if (dateEl) dateEl.min = new Date().toISOString().slice(0, 10);

        var selSvc = popup.querySelector('[name="servico_id"]');
        var inIni  = popup.querySelector('[name="hora_inicio"]');
        var inFim  = popup.querySelector('[name="hora_fim"]');
        var info   = popup.querySelector('#novoag-duracao-info');
        var fimEl  = popup.querySelector('#novoag-hora-fim-display');

        function calcFim() {
          if (!selSvc || !inIni || !inFim) return;
          var opt = selSvc.options[selSvc.selectedIndex];
          var dur = parseInt(opt ? (opt.dataset.duracao || '0') : '0', 10);
          var val = inIni.value;
          if (!val || !dur) { if (info) info.style.display = 'none'; return; }
          var p = val.split(':');
          var total = parseInt(p[0], 10) * 60 + parseInt(p[1], 10) + dur;
          var hF = String(Math.floor(total / 60)).padStart(2, '0');
          var mF = String(total % 60).padStart(2, '0');
          inFim.value = hF + ':' + mF;
          if (fimEl) fimEl.textContent = hF + ':' + mF;
          if (info)  info.style.display = '';
        }

        if (selSvc) selSvc.addEventListener('change', calcFim);
        if (inIni)  inIni.addEventListener('change', calcFim);
      },
      preConfirm: function () {
        var p    = document.querySelector('.swal2-popup');
        var svc  = p.querySelector('[name="servico_id"]').value;
        var data = p.querySelector('[name="data"]').value;
        var ini  = p.querySelector('[name="hora_inicio"]').value;
        var fim  = p.querySelector('[name="hora_fim"]').value || ini;

        if (!svc)  { Swal.showValidationMessage('Selecione um serviço.');          return false; }
        if (!data) { Swal.showValidationMessage('Informe a data.');                 return false; }
        if (!ini)  { Swal.showValidationMessage('Informe o horário de início.');    return false; }

        return fetch('../../api/user/agendamentos.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'criar', servico_id: svc, data: data, hora_inicio: ini, hora_fim: fim })
        }).then(function (r) { return r.json(); });
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({
          icon: 'success',
          title: 'Agendado!',
          text: 'Agendamento criado com sucesso! Você receberá uma confirmação pelo WhatsApp. ✅',
          timer: 2800,
          showConfirmButton: false
        }).then(function () { location.reload(); });
      } else {
        SwalTP.fire({ icon: 'error', title: 'Erro', text: (result.value && result.value.message) || 'Não foi possível criar o agendamento.' });
      }
    });
  }

  // --- Cancelar Agendamento ---
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-cancelar-id]');
    if (!btn) return;
    e.preventDefault();

    var id  = btn.dataset.cancelarId;
    var svc = btn.dataset.servico || 'este agendamento';

    SwalTP.fire({
      title: 'Cancelar Agendamento',
      html: 'Tem certeza que deseja cancelar <strong>' + svc + '</strong>?<br><small style="color:#888;">Esta ação não pode ser desfeita.</small>',
      showCancelButton: true,
      confirmButtonText: 'Sim, cancelar',
      cancelButtonText: 'Voltar',
      customClass: {
        confirmButton: 'swal-tp__btn swal-tp__btn--danger',
        cancelButton:  'swal-tp__btn swal-tp__btn--cancel'
      },
      preConfirm: function () {
        return fetch('../../api/user/agendamentos.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'cancelar', id: id })
        }).then(function (r) { return r.json(); });
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (result.value && result.value.success) {
        SwalTP.fire({ icon: 'success', title: 'Cancelado!', timer: 1500, showConfirmButton: false })
          .then(function () { location.reload(); });
      } else {
        SwalTP.fire({ icon: 'error', title: 'Erro', text: (result.value && result.value.message) || 'Não foi possível cancelar.' });
      }
    });
  });
})();

  if (btnNovo) btnNovo.addEventListener('click', function () { abrirModalNovoAgendamento(); });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-modal="modal-novo-agendamento"]');
    if (btn) { e.preventDefault(); abrirModalNovoAgendamento(); }
  });

  function abrirModalNovoAgendamento() {
    var overlay = document.getElementById('modal-novo-agendamento');
    SwalTP.fire({
      title: 'Novo Agendamento',
      html: overlay.querySelector('.modal-body').innerHTML,
      showCancelButton: true,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      width: '520px',
      didOpen: function (popup) {
        var dateEl = popup.querySelector('[name="data"]');
        if (dateEl) dateEl.min = new Date().toISOString().slice(0, 10);
        var selSvc = popup.querySelector('[name="servico_id"]');
        var inIni  = popup.querySelector('[name="hora_inicio"]');
        var inFim  = popup.querySelector('[name="hora_fim"]');
        var info   = popup.querySelector('#novoag-duracao-info');
        var fimEl  = popup.querySelector('#novoag-hora-fim-display');
        function calcFim() {
          var opt = selSvc.options[selSvc.selectedIndex];
          var dur = parseInt(opt ? (opt.dataset.duracao || '0') : '0', 10);
          var val = inIni.value;
          var p = val.split(':'), total = parseInt(p[0],10)*60+parseInt(p[1],10)+dur;
          var hF = String(Math.floor(total/60)).padStart(2,'0');
          var mF = String(total%60).padStart(2,'0');
          inFim.value = hF+':'+mF;
          if (fimEl) fimEl.textContent = hF+':'+mF;
          if (info)  info.style.display = '';
        }
        if (selSvc) selSvc.addEventListener('change', calcFim);
        if (inIni)  inIni.addEventListener('change', calcFim);
      },
      preConfirm: function () {
        var p = document.querySelector('.swal2-popup');
        var svc  = p.querySelector('[name="servico_id"]').value;
        var data = p.querySelector('[name="data"]').value;
        var ini  = p.querySelector('[name="hora_inicio"]').value;
        var fim  = p.querySelector('[name="hora_fim"]').value || ini;
        return fetch('../../api/user/agendamentos.php', {
          method: 'POST', headers: {'Content-Type':'application/json'},
          body: JSON.stringify({action:'criar',servico_id:svc,data:data,hora_inicio:ini,hora_fim:fim})
        }).then(function(r){return r.json();});
      }
    }).then(function(result){
      if (result.value && result.value.success) {
        SwalTP.fire({icon:'success',title:'Agendado!',text:'Confirmacao enviada pelo WhatsApp!',timer:2800,showConfirmButton:false})
          .then(function(){location.reload();});
      } else {
        SwalTP.fire({icon:'error',title:'Erro',text:(result.value&&result.value.message)||'Nao foi possivel criar.'});
      }
    });
  }

  document.addEventListener('click', function(e){
    var btn = e.target.closest('[data-cancelar-id]');
    e.preventDefault();
    var id = btn.dataset.cancelarId, svc = btn.dataset.servico||'este agendamento';
    SwalTP.fire({
      title:'Cancelar Agendamento',
      html:'Cancelar <strong>'+svc+'</strong>? <br><small style="color:#888">Esta acao nao pode ser desfeita.</small>',
      showCancelButton:true, confirmButtonText:'Sim, cancelar', cancelButtonText:'Voltar',
      customClass:{confirmButton:'swal-tp__btn swal-tp__btn--danger',cancelButton:'swal-tp__btn swal-tp__btn--cancel'},
      preConfirm:function(){
        return fetch('../../api/user/agendamentos.php',{
          method:'POST',headers:{'Content-Type':'application/json'},
          body:JSON.stringify({action:'cancelar',id:id})
        }).then(function(r){return r.json();});
      }
    }).then(function(result){
      if (result.value && result.value.success) {
        SwalTP.fire({icon:'success',title:'Cancelado!',timer:1500,showConfirmButton:false}).then(function(){location.reload();});
      } else {
        SwalTP.fire({icon:'error',title:'Erro',text:(result.value&&result.value.message)||'Nao foi possivel cancelar.'});
      }
    });
  });
})();
(function () {
  var BASE = window.API_BASE || '../../api/';

  function request(url, options) {
    return fetch(url, options).then(function (response) {
      return response.json().catch(function () { return { success: false, message: 'Resposta inválida do servidor.' }; });
    });
  }

  function escapeHtml(value) {
    var element = document.createElement('div');
    element.textContent = value || '';
    return element.innerHTML;
  }

  function formatarData(data) {
    var partes = data.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
  }

  function somarMinutos(hora, minutos) {
    var partes = hora.split(':');
    var total = Number(partes[0]) * 60 + Number(partes[1]) + minutos;
    return String(Math.floor(total / 60)).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
  }

  function classeDeSlots(duracao) {
    var slots = Math.max(1, Math.ceil(Number(duracao || 30) / 30));
    return "agenda-appt--slots-" + Math.min(24, slots);
  }

  function abrirFormulario(data, hora, agendamento) {
    var overlay = document.getElementById('modal-novo-agendamento');
    if (!overlay) return;
    var editando = Boolean(agendamento);

    SwalTP.fire({
      title: editando ? 'Editar agendamento' : 'Novo agendamento',
      html: overlay.querySelector('.modal-body').innerHTML,
      showCancelButton: true,
      confirmButtonText: editando ? 'Salvar alterações' : 'Confirmar agendamento',
      cancelButtonText: 'Cancelar',
      width: '560px',
      didOpen: function (popup) {
        popup.querySelector('[name="data"]').value = data;
        popup.querySelector('[name="hora_inicio"]').value = hora;
        popup.querySelector('[data-agendamento-horario]').textContent = formatarData(data) + ' às ' + hora;

        var selecionados = editando ? agendamento.servicos.map(function (servico) { return String(servico.id); }) : [];
        popup.querySelectorAll('[name="servicos_ids"]').forEach(function (input) {
          input.checked = selecionados.indexOf(input.value) !== -1;
          input.addEventListener('change', atualizarResumo);
        });
        if (editando) popup.querySelector('[name="observacoes"]').value = agendamento.observacoes || '';

        function atualizarResumo() {
          var inputs = Array.prototype.slice.call(popup.querySelectorAll('[name="servicos_ids"]:checked'));
          var resumo = popup.querySelector('[data-agendamento-resumo]');
          if (!inputs.length) { resumo.hidden = true; return; }
          var minutos = inputs.reduce(function (total, input) { return total + Number(input.dataset.duracao); }, 0);
          var valor = inputs.reduce(function (total, input) { return total + Number(input.dataset.preco); }, 0);
          resumo.hidden = false;
          popup.querySelector('[data-agendamento-lista]').textContent = inputs.map(function (input) { return input.parentElement.querySelector('span').textContent; }).join(', ');
          popup.querySelector('[data-agendamento-total]').textContent = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
          popup.querySelector('[data-agendamento-duracao]').textContent = minutos + ' min';
          popup.querySelector('[data-agendamento-fim]').textContent = somarMinutos(hora, minutos);
        }
        atualizarResumo();
      },
      preConfirm: function () {
        var popup = Swal.getPopup();
        var servicosIds = Array.prototype.slice.call(popup.querySelectorAll('[name="servicos_ids"]:checked')).map(function (input) { return Number(input.value); });
        if (!servicosIds.length) {
          Swal.showValidationMessage('Selecione pelo menos um serviço.');
          return false;
        }
        var payload = {
          action: editando ? 'editar' : 'criar',
          data: data,
          hora_inicio: hora,
          servicos_ids: servicosIds,
          observacoes: popup.querySelector('[name="observacoes"]').value.trim()
        };
        if (editando) payload.id = agendamento.id;
        return request(BASE + 'user/agendamentos.php', {
          method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
        });
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      if (!result.value || !result.value.success) {
        SwalTP.fire({ icon: 'error', title: 'Não foi possível salvar', text: (result.value && result.value.message) || 'Tente novamente.' });
        return;
      }
      atualizarAgenda().then(function () {
        SwalTP.fire({ icon: 'success', title: editando ? 'Agendamento atualizado!' : 'Agendamento confirmado!', timer: 1600, showConfirmButton: false });
      });
    });
  }

  function abrirDetalhes(id) {
    request(BASE + 'user/agendamentos.php?action=detalhar&id=' + encodeURIComponent(id)).then(function (resposta) {
      if (!resposta.success) { SwalTP.fire({ icon: 'error', title: 'Erro', text: resposta.message }); return; }
      var ag = resposta.data;
      var servicos = ag.servicos.map(function (servico) { return '<li>' + escapeHtml(servico.nome) + '</li>'; }).join('');
      var html = '<div class="agendamento-detalhes">'
        + '<p><strong>Data:</strong> ' + escapeHtml(ag.data_fmt) + '</p>'
        + '<p><strong>Horário:</strong> ' + escapeHtml(ag.hora_inicio.slice(0, 5)) + ' às ' + escapeHtml(ag.hora_fim.slice(0, 5)) + '</p>'
        + '<p><strong>Serviços:</strong></p><ul>' + servicos + '</ul>'
        + '<p><strong>Total:</strong> ' + Number(ag.preco_servico).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</p>'
        + '<p><strong>Duração:</strong> ' + ag.duracao_minutos + ' min</p>'
        + '<p><strong>Observações:</strong> ' + escapeHtml(ag.observacoes || 'Nenhuma') + '</p>'
        + '<p><strong>Status:</strong> ' + escapeHtml(ag.status) + '</p></div>';
      SwalTP.fire({
        title: 'Meu agendamento', html: html, showDenyButton: !['cancelado', 'finalizado'].includes(ag.status), showCancelButton: true,
        confirmButtonText: 'Editar', denyButtonText: 'Cancelar agendamento', cancelButtonText: 'Excluir',
        preConfirm: function () { return { acao: 'editar' }; },
        preDeny: function () { return confirmarCancelamento(ag.id); }
      }).then(function (result) {
        if (result.isConfirmed && result.value && result.value.acao === 'editar') abrirFormulario(ag.data, ag.hora_inicio.slice(0, 5), ag);
        if (result.isDenied && result.value && result.value.success) atualizarAgenda();
        if (result.dismiss === Swal.DismissReason.cancel) confirmarExclusao(ag.id);
      });
    });
  }

  function confirmarCancelamento(id) {
    return request(BASE + 'user/agendamentos.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'cancelar', id: id })
    }).then(function (resposta) {
      if (!resposta.success) {
        Swal.showValidationMessage(resposta.message || 'Não foi possível cancelar.');
        return false;
      }
      return resposta;
    });
  }

  function confirmarExclusao(id) {
    SwalTP.fire({
      title: 'Excluir agendamento?',
      text: 'Esta ação não poderá ser desfeita.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Voltar',
      customClass: {
        popup: 'swal-tp',
        title: 'swal-tp__title',
        htmlContainer: 'swal-tp__body',
        confirmButton: 'swal-tp__btn swal-tp__btn--danger',
        cancelButton: 'swal-tp__btn swal-tp__btn--cancel',
        actions: 'swal-tp__actions',
        closeButton: 'swal-tp__close'
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      request(BASE + 'user/agendamentos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'excluir', id: id })
      }).then(function (resposta) {
        if (!resposta.success) {
          SwalTP.fire({ icon: 'error', title: 'Erro', text: resposta.message || 'Não foi possível excluir.' });
          return;
        }
        atualizarAgenda().then(function () {
          SwalTP.fire({ icon: 'success', title: 'Agendamento excluído', timer: 1500, showConfirmButton: false });
        });
      });
    });
  }

  function atualizarAgenda() {
    var cells = Array.prototype.slice.call(document.querySelectorAll('.agenda-cell[data-date][data-time]'));
    if (!cells.length) return Promise.resolve();
    var datas = cells.map(function (cell) { return cell.dataset.date; }).sort();
    var inicio = datas[0], fim = datas[datas.length - 1];
    return request(BASE + 'user/agendamentos.php?action=agenda&inicio=' + inicio + '&fim=' + fim).then(function (resposta) {
      if (!resposta.success) return;
      var porCelula = {};
      resposta.data.forEach(function (ag) {
        var inicioMin = Number(ag.hora_inicio.slice(0, 2)) * 60 + Number(ag.hora_inicio.slice(3, 5));
        var fimMin = Number(ag.hora_fim.slice(0, 2)) * 60 + Number(ag.hora_fim.slice(3, 5));
        for (var minuto = inicioMin; minuto < fimMin; minuto += 30) {
          var hora = String(Math.floor(minuto / 60)).padStart(2, '0') + ':' + String(minuto % 60).padStart(2, '0');
          porCelula[ag.data + '|' + hora] = { ag: ag, inicio: minuto === inicioMin };
        }
      });
      cells.forEach(function (cell) {
        var entrada = porCelula[cell.dataset.date + '|' + cell.dataset.time];
        if (!entrada) {
          cell.innerHTML = '<button class="agenda-cell__add" type="button" data-date="' + cell.dataset.date + '" data-time="' + cell.dataset.time + '"><span>Disponível</span><small>' + cell.dataset.time + '</small></button>';
        } else if (!entrada.inicio) {
          cell.innerHTML = '<div class="agenda-cell__blocked" aria-hidden="true"></div>';
        } else if (entrada.ag.proprio) {
          cell.innerHTML = '<button class="agenda-appt agenda-appt--meu ' + classeDeSlots(entrada.ag.duracao_minutos) + '" type="button" data-own-id="' + entrada.ag.id + '"><span class="agenda-appt__name">Seu agendamento</span><span class="agenda-appt__service">' + escapeHtml(entrada.ag.servico) + ' · ' + Number(entrada.ag.duracao_minutos || 30) + ' min</span></button>';
        } else {
          cell.innerHTML = '<div class="agenda-appt agenda-appt--ocupado ' + classeDeSlots(entrada.ag.duracao_minutos) + '"><span class="agenda-appt__name">Ocupado</span></div>';
        }
      });
    });
  }

  document.addEventListener('click', function (event) {
    var cancelar = event.target.closest('[data-cancelar-id]');
    if (cancelar) {
      event.preventDefault();
      event.stopPropagation();
      confirmarCancelamentoComDialogo(cancelar.dataset.cancelarId, cancelar.dataset.servico || 'este serviço');
      return;
    }
    var livre = event.target.closest('.agenda-cell__add');
    if (livre) { abrirFormulario(livre.dataset.date, livre.dataset.time); return; }
    var proprio = event.target.closest('[data-own-id]');
    if (proprio) abrirDetalhes(proprio.dataset.ownId);
  });

  function confirmarCancelamentoComDialogo(id, servico) {
    SwalTP.fire({
      title: 'Cancelar agendamento?',
      text: 'O horário de ' + servico + ' será liberado.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Cancelar agendamento',
      cancelButtonText: 'Voltar',
      customClass: {
        popup: 'swal-tp',
        title: 'swal-tp__title',
        htmlContainer: 'swal-tp__body',
        confirmButton: 'swal-tp__btn swal-tp__btn--danger',
        cancelButton: 'swal-tp__btn swal-tp__btn--cancel',
        actions: 'swal-tp__actions',
        closeButton: 'swal-tp__close'
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      confirmarCancelamento(id).then(function (resposta) {
        if (!resposta.success) {
          SwalTP.fire({ icon: 'error', title: 'Erro', text: resposta.message || 'Não foi possível cancelar.' });
          return;
        }
        atualizarAgenda().then(function () {
          SwalTP.fire({ icon: 'success', title: 'Agendamento cancelado', timer: 1500, showConfirmButton: false }).then(function () {
            window.location.reload();
          });
        });
      });
    });
  }

  atualizarAgenda();
})();

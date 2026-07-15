(function () {
  var base = window.API_BASE || '../../api/';

  function request(payload) {
    return fetch(base + 'user/carrinho.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); });
  }
  function refresh() { window.location.reload(); }

  // ── Quantidade / remoção de itens ──
  document.addEventListener('click', function (event) {
    var inc = event.target.closest('[data-page-inc]');
    var dec = event.target.closest('[data-page-dec]');
    var remove = event.target.closest('[data-page-remove]');
    if (inc || dec) {
      var id = (inc || dec).dataset.pageInc || (inc || dec).dataset.pageDec;
      var row = (inc || dec).closest('[data-cart-page-item]');
      var current = Number(row.querySelector('.cart-page-item__quantity strong').textContent);
      request({ action: 'atualizar', item_id: id, quantidade: inc ? current + 1 : current - 1 }).then(function (r) { if (r.success) refresh(); else SwalTP.erro('Não foi possível atualizar', r.message); });
    }
    if (remove) request({ action: 'remover', item_id: remove.dataset.pageRemove }).then(function (r) { if (r.success) refresh(); });
  });

  // ── Formulário de endereço ──
  var form = document.getElementById('cart-address-form');
  if (!form) return;

  var campos = {
    cep: document.getElementById('cart-cep'),
    logradouro: document.getElementById('cart-logradouro'),
    numero: document.getElementById('cart-numero'),
    bairro: document.getElementById('cart-bairro'),
    cidade: document.getElementById('cart-cidade'),
    estado: document.getElementById('cart-estado'),
    complemento: document.getElementById('cart-complemento'),
    ponto_referencia: document.getElementById('cart-referencia')
  };
  var checkout = document.getElementById('cart-page-checkout');
  var cepStatus = document.getElementById('cart-cep-status');

  function apenasDigitos(valor) { return (valor || '').replace(/\D/g, ''); }

  function mascaraCep(valor) {
    var d = apenasDigitos(valor).slice(0, 8);
    return d.length > 5 ? d.slice(0, 5) + '-' + d.slice(5) : d;
  }

  function mostrarErro(campo, mensagem) {
    var input = campos[campo];
    if (input) input.classList.add('is-invalid');
    var alvo = form.querySelector('[data-error-for="' + campo + '"]');
    if (alvo) { alvo.textContent = mensagem; alvo.hidden = false; }
  }

  function limparErro(campo) {
    var input = campos[campo];
    if (input) input.classList.remove('is-invalid');
    var alvo = form.querySelector('[data-error-for="' + campo + '"]');
    if (alvo) { alvo.textContent = ''; alvo.hidden = true; }
  }

  function limparTodosErros() {
    Object.keys(campos).forEach(limparErro);
  }

  // Validação de front-end. Retorna o payload ou null (se houver erros).
  function validar() {
    limparTodosErros();
    var erros = 0;

    var cep = apenasDigitos(campos.cep.value);
    if (cep.length !== 8) { mostrarErro('cep', 'Informe um CEP válido (8 números).'); erros++; }

    [['logradouro', 'Informe o logradouro.'],
     ['numero', 'Informe o número (ou S/N).'],
     ['bairro', 'Informe o bairro.'],
     ['cidade', 'Informe a cidade.']].forEach(function (par) {
      if (!campos[par[0]].value.trim()) { mostrarErro(par[0], par[1]); erros++; }
    });

    if (!campos.estado.value) { mostrarErro('estado', 'Selecione o estado (UF).'); erros++; }

    if (erros > 0) {
      var primeiro = form.querySelector('.is-invalid');
      if (primeiro) primeiro.focus();
      return null;
    }

    return {
      action: 'finalizar',
      cep: mascaraCep(campos.cep.value),
      logradouro: campos.logradouro.value.trim(),
      numero: campos.numero.value.trim(),
      bairro: campos.bairro.value.trim(),
      cidade: campos.cidade.value.trim(),
      estado: campos.estado.value,
      complemento: campos.complemento.value.trim(),
      ponto_referencia: campos.ponto_referencia.value.trim()
    };
  }

  // Máscara + limpeza de erro ao digitar
  campos.cep.addEventListener('input', function () {
    campos.cep.value = mascaraCep(campos.cep.value);
    limparErro('cep');
    if (apenasDigitos(campos.cep.value).length === 8) buscarCep();
  });
  Object.keys(campos).forEach(function (nome) {
    if (nome === 'cep') return;
    campos[nome].addEventListener('input', function () { limparErro(nome); });
    campos[nome].addEventListener('change', function () { limparErro(nome); });
  });

  // ── Consulta automática de CEP (ViaCEP) ──
  // Preenche logradouro/bairro/cidade/estado. Campos continuam editáveis.
  // Se a API falhar, o formulário segue funcionando normalmente.
  function buscarCep() {
    var cep = apenasDigitos(campos.cep.value);
    if (cep.length !== 8) return;
    if (cepStatus) cepStatus.textContent = 'Buscando…';

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
      .then(function (r) { return r.json(); })
      .then(function (dados) {
        if (cepStatus) cepStatus.textContent = '';
        if (!dados || dados.erro) { return; } // CEP inexistente: preenchimento manual
        if (dados.logradouro) campos.logradouro.value = dados.logradouro;
        if (dados.bairro) campos.bairro.value = dados.bairro;
        if (dados.localidade) campos.cidade.value = dados.localidade;
        if (dados.uf) campos.estado.value = dados.uf;
        limparErro('logradouro'); limparErro('bairro'); limparErro('cidade'); limparErro('estado');
        // Foca o número, que o ViaCEP não fornece.
        campos.numero.focus();
      })
      .catch(function () {
        if (cepStatus) cepStatus.textContent = ''; // falha da API: segue manual
      });
  }

  // ── Envio do pedido ──
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    var payload = validar();
    if (!payload) return; // não envia com erros; dados preservados

    payload.csrf_token = (document.getElementById('cart-csrf') || {}).value || '';
    checkout.disabled = true;

    fetch(base + 'user/pedidos.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': payload.csrf_token },
      body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); }).then(function (resposta) {
      if (!resposta || !resposta.success) {
        checkout.disabled = false;
        SwalTP.erro('Não foi possível fazer o pedido', (resposta && resposta.message) || 'Tente novamente.');
        return;
      }
      SwalTP.fire({ icon: 'success', title: 'Pedido realizado!', text: resposta.message, confirmButtonText: 'Ver meus pedidos' })
        .then(function () { window.location.href = 'pedidos.php'; });
    }).catch(function () {
      checkout.disabled = false;
      SwalTP.erro('Erro de conexão', 'Não foi possível conectar ao servidor.');
    });
  });
})();

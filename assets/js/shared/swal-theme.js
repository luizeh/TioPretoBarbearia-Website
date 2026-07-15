// swal-theme.js — Tema SweetAlert2 para Tio Preto Barbearia
// Fonte única de configuração dos alertas/modais do projeto.
// - SwalTP        → tema padrão (botão confirmar dourado)
// - SwalTP.danger → variante com botão de confirmação de ação perigosa
// - Helpers reutilizáveis: confirmarExclusao, confirmarCancelamento,
//   confirmarEdicao, sucesso, erro, aviso — evitam configs duplicadas.
(function () {
  "use strict";

  // customClass base — replicado nas duas mixins para evitar o problema do
  // merge raso do SweetAlert (passar customClass em .fire() substitui o objeto
  // inteiro; por isso NUNCA passamos customClass parcial nas chamadas).
  var baseClasses = {
    popup: "swal-tp",
    title: "swal-tp__title",
    htmlContainer: "swal-tp__body",
    confirmButton: "swal-tp__btn swal-tp__btn--confirm",
    denyButton: "swal-tp__btn swal-tp__btn--danger",
    cancelButton: "swal-tp__btn swal-tp__btn--cancel",
    actions: "swal-tp__actions",
    closeButton: "swal-tp__close",
  };

  var baseConfig = {
    customClass: baseClasses,
    buttonsStyling: false,
    showCloseButton: true,
    backdrop: "rgba(26,26,26,0.65)",
    allowOutsideClick: true,
  };

  var SwalTP = Swal.mixin(baseConfig);

  // Variante "perigosa": o botão de confirmação recebe o estilo de ação
  // destrutiva (vermelho). Usada em exclusões e cancelamentos.
  function dangerClasses() {
    var c = {};
    for (var k in baseClasses) c[k] = baseClasses[k];
    c.confirmButton = "swal-tp__btn swal-tp__btn--danger";
    return c;
  }
  SwalTP.danger = Swal.mixin({
    customClass: dangerClasses(),
    buttonsStyling: false,
    showCloseButton: true,
    backdrop: "rgba(26,26,26,0.65)",
    allowOutsideClick: true,
  });

  // ── Helpers de confirmação (retornam a Promise do SweetAlert) ──
  // Cada helper aplica os padrões e faz merge das opções passadas, então
  // qualquer chave extra (html, preConfirm, didOpen, width…) é repassada.

  // Confirmação de exclusão (ação perigosa, botão vermelho).
  SwalTP.confirmarExclusao = function (opcoes) {
    return SwalTP.danger.fire(Object.assign({
      icon: "warning",
      title: "Excluir?",
      text: "Esta ação não poderá ser desfeita.",
      showCancelButton: true,
      confirmButtonText: "Sim, excluir",
      cancelButtonText: "Voltar",
      reverseButtons: true,
      focusCancel: true,
    }, opcoes || {}));
  };

  // Confirmação de cancelamento (ação perigosa, botão vermelho).
  SwalTP.confirmarCancelamento = function (opcoes) {
    return SwalTP.danger.fire(Object.assign({
      icon: "warning",
      title: "Cancelar?",
      showCancelButton: true,
      confirmButtonText: "Sim, cancelar",
      cancelButtonText: "Voltar",
      reverseButtons: true,
      focusCancel: true,
    }, opcoes || {}));
  };

  // Confirmação de edição (ação comum, botão dourado).
  SwalTP.confirmarEdicao = function (opcoes) {
    return SwalTP.fire(Object.assign({
      icon: "question",
      title: "Salvar alterações?",
      showCancelButton: true,
      confirmButtonText: "Salvar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    }, opcoes || {}));
  };

  // Toast/aviso de sucesso.
  SwalTP.sucesso = function (titulo, texto) {
    return SwalTP.fire({
      icon: "success",
      title: titulo || "Tudo certo!",
      text: texto,
      timer: 1600,
      showConfirmButton: false,
    });
  };

  // Alerta de erro.
  SwalTP.erro = function (titulo, texto) {
    return SwalTP.fire({
      icon: "error",
      title: titulo || "Ops!",
      text: texto || "Ocorreu um erro. Tente novamente.",
      confirmButtonText: "Ok",
    });
  };

  // Aviso genérico.
  SwalTP.aviso = function (titulo, texto) {
    return SwalTP.fire({
      icon: "warning",
      title: titulo || "Atenção",
      text: texto,
      confirmButtonText: "Ok",
    });
  };

  window.SwalTP = SwalTP;
})();

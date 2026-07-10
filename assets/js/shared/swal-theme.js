// swal-theme.js — Tema SweetAlert2 para Tio Preto Barbearia
(function () {
  window.SwalTP = Swal.mixin({
    customClass: {
      popup: "swal-tp",
      title: "swal-tp__title",
      htmlContainer: "swal-tp__body",
      confirmButton: "swal-tp__btn swal-tp__btn--confirm",
      cancelButton: "swal-tp__btn swal-tp__btn--cancel",
      actions: "swal-tp__actions",
      closeButton: "swal-tp__close",
    },
    buttonsStyling: false,
    showCloseButton: true,
    backdrop: "rgba(26,26,26,0.65)",
    allowOutsideClick: true,
  });
})();

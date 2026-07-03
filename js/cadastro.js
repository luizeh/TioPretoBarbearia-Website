const telefone = document.getElementById("telefone");

telefone.addEventListener("input", function () {
  let valor = this.value.replace(/\D/g, "");

  if (valor.startsWith("55")) {
    valor = valor.substring(2);
  }
  valor = valor.substring(0, 11);

  let formatado = "+55 ";

  if (valor.length > 0) {
    formatado += "(" + valor.substring(0, 2);
  }

  if (valor.length >= 3) {
    formatado += ") " + valor.substring(2, 7);
  }

  if (valor.length >= 8) {
    formatado += "-" + valor.substring(7, 11);
  }

  this.value = formatado;
});

function avaliarSenha(valor) {
  const bars = [
    document.getElementById("s1"),
    document.getElementById("s2"),
    document.getElementById("s3"),
    document.getElementById("s4"),
  ];
  const cores = ["#e74c3c", "#e67e22", "#f1c40f", "#c9963a"];

  let nivel = 0;
  if (valor.length >= 6) nivel++;
  if (valor.length >= 10) nivel++;
  if (/[A-Z]/.test(valor) && /[0-9]/.test(valor)) nivel++;
  if (/[^A-Za-z0-9]/.test(valor)) nivel++;

  bars.forEach((bar, i) => {
    bar.style.background =
      i < nivel ? cores[nivel - 1] : "rgba(255,255,255,0.1)";
  });
}

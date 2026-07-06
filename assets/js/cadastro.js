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

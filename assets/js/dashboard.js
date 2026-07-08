// Data no topbar
const d = new Date();
const opts = {
  weekday: "long",
  day: "2-digit",
  month: "long",
  year: "numeric",
};

document.getElementById("topbarDate").textContent = d
  .toLocaleDateString("pt-BR", opts)
  .replace(/^\w/, (c) => c.toUpperCase());

// Toggle sidebar mobile
document.getElementById("sidebarToggle").addEventListener("click", () => {
  document.getElementById("sidebar").classList.toggle("sidebar--open");
});

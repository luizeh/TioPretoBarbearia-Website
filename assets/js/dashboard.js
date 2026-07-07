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

// ── Sistema de modais ──
document.querySelectorAll("[data-modal]").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    document.getElementById(btn.dataset.modal).classList.add("modal--open");
  });
});

document.querySelectorAll("[data-close]").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.getElementById(btn.dataset.close).classList.remove("modal--open");
  });
});

document.querySelectorAll(".modal-overlay").forEach((overlay) => {
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) overlay.classList.remove("modal--open");
  });
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    document.querySelectorAll(".modal-overlay.modal--open").forEach((m) => {
      m.classList.remove("modal--open");
    });
  }
});

// Busca e filtro por data compartilhados pelas tabelas administrativas.
(function () {
  "use strict";

  function filterTable(table) {
    var searchInput = document.querySelector('[data-search="' + table.id + '"]');
    var dateInput = document.querySelector('[data-filter-date="' + table.id + '"]');
    var query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    var selectedDate = dateInput ? dateInput.value : "";

    table.querySelectorAll("tbody tr").forEach(function (row) {
      var matchesSearch = !query || row.textContent.toLowerCase().includes(query);
      var matchesDate = !selectedDate || row.dataset.date === selectedDate;
      row.hidden = !(matchesSearch && matchesDate);
    });
  }

  document.querySelectorAll("[data-search], [data-filter-date]").forEach(function (input) {
    var tableId = input.dataset.search || input.dataset.filterDate;
    var table = document.getElementById(tableId);
    if (!table) return;

    input.addEventListener(input.dataset.search ? "input" : "change", function () {
      filterTable(table);
    });
  });
})();

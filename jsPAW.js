document.addEventListener("DOMContentLoaded", () => {
  // Elements
  const navItems = document.querySelectorAll(".nav-item");
  const sections = document.querySelectorAll(".section");
  const toggleSidebarBtn = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");

  const table = document.getElementById("attendanceTable");
  const tbody = table.querySelector("tbody");

  const studentForm = document.getElementById("studentForm");
  const studentId = document.getElementById("studentId");
  const lastName = document.getElementById("lastName");
  const firstName = document.getElementById("firstName");
  const emailField = document.getElementById("email");

  const idError = document.getElementById("idError");
  const lastError = document.getElementById("lastError");
  const firstError = document.getElementById("firstError");
  const emailError = document.getElementById("emailError");

  const highlightBtn = document.getElementById("highlightExcellent");
  const resetBtn = document.getElementById("resetColors");

  // Exercice 7: Search and Sort
  const searchInput = document.getElementById("searchName");
  const sortAbsBtn = document.getElementById("sortByAbsences");
  const sortParBtn = document.getElementById("sortByParticipation");
  const sortStatus = document.getElementById("sortStatus");

  const reportText = document.getElementById("reportText");
  const reportCanvas = document.getElementById("reportChart");
  let reportChart = null;

  const toast = document.getElementById("toast");

  // NAVIGATION
  navItems.forEach(item => {
    item.addEventListener("click", () => {
      navItems.forEach(n => n.classList.remove("active"));
      item.classList.add("active");
      const target = item.dataset.target;
      sections.forEach(s => s.classList.remove("active-section"));
      document.getElementById(target).classList.add("active-section");
      if (target === "reportSection") renderReport();
    });
  });

  // SIDEBAR TOGGLE
  toggleSidebarBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
  });

  // Utility: toast
  function showToast(msg) {
    if (!toast) { alert(msg); return; }
    toast.textContent = msg;
    toast.style.display = "block";
    toast.style.opacity = "1";
    if (toast._timeout) clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => { toast.style.display = "none"; }, 300);
    }, 1600);
  }

  // Update row: counts, classes and message
  function updateRow(row) {
    const cells = row.querySelectorAll("td");
    if (!cells || cells.length < 17) return;
    let abs = 0, par = 0;
    for (let i = 2; i <= 12; i += 2) if (cells[i].textContent.trim() === "") abs++;
    for (let i = 3; i <= 13; i += 2) if (cells[i].textContent.trim() === "✓") par++;
    cells[14].textContent = abs + " Abs";
    cells[15].textContent = par + " Par";
    row.classList.remove("green","yellow","red");
    if (abs < 3) row.classList.add("green");
    else if (abs <= 4) row.classList.add("yellow");
    else row.classList.add("red");

    // message
    let msg = "";
    if (abs < 3 && par >= 3) msg = "Good attendance — Excellent participation";
    else if (abs >= 5) msg = "Excluded — too many absences";
    else if (abs < 3 && par < 3) msg = "Good attendance — You need to participate more";
    else if (abs >= 3 && par >= 3) msg = "Warning — low attendance but good participation";
    else msg = "Warning — low attendance and low participation";
    cells[16].textContent = msg;
  }

  // Initialize existing rows
  tbody.querySelectorAll("tr").forEach(r => updateRow(r));

  // EVENT DELEGATION for clickable cells
  tbody.addEventListener("click", (ev) => {
    const td = ev.target.closest("td.clickable");
    if (!td) return;
    const isParticipation = (td.cellIndex % 2 === 1);
    const wasEmpty = td.textContent.trim() === "";
    td.textContent = wasEmpty ? "✓" : "";
    updateRow(td.parentElement);
    showToast(isParticipation ? (wasEmpty ? "Participation ajoutée ✔️" : "Participation retirée ❌")
                              : (wasEmpty ? "Présence ajoutée ✔️" : "Présence retirée ❌"));
  });

  // EXERCICE 5: Hover et clic sur les lignes
  tbody.addEventListener("mouseover", (e) => {
    const row = e.target.closest("tr");
    if (row) row.classList.add("highlight");
  });

  tbody.addEventListener("mouseout", (e) => {
    const row = e.target.closest("tr");
    if (row) row.classList.remove("highlight");
  });

  tbody.addEventListener("click", (e) => {
    const row = e.target.closest("tr");
    if (!row || e.target.classList.contains("clickable")) return;
    
    const lastName = row.cells[0].textContent;
    const firstName = row.cells[1].textContent;
    const absences = row.cells[14].textContent;
    
    alert(`📋 Étudiant : ${firstName} ${lastName}\n${absences}`);
  });

  // ADD STUDENT
  studentForm.addEventListener("submit", (e) => {
    e.preventDefault();
    idError.textContent = lastError.textContent = firstError.textContent = emailError.textContent = "";
    const id = studentId.value.trim();
    const last = lastName.value.trim();
    const first = firstName.value.trim();
    const email = emailField.value.trim();
    let valid = true;
    if (!/^[0-9]+$/.test(id)) { idError.textContent = "ID invalide"; valid = false; }
    if (!/^[A-Za-z]+$/.test(last)) { lastError.textContent = "Nom invalide"; valid = false; }
    if (!/^[A-Za-z]+$/.test(first)) { firstError.textContent = "Prénom invalide"; valid = false; }
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { emailError.textContent = "Email invalide"; valid = false; }
    if (!valid) return;

    const tr = document.createElement("tr");
    let inner = `<td>${last}</td><td>${first}</td>`;
    for (let k=0;k<6;k++) inner += "<td class='clickable'></td><td class='clickable'></td>";
    inner += "<td></td><td></td><td></td>";
    tr.innerHTML = inner;
    tbody.appendChild(tr);
    updateRow(tr);
    showToast("Étudiant ajouté ✅");
    studentForm.reset();
  });

  // HIGHLIGHT EXCELLENT
  highlightBtn.addEventListener("click", () => {
    let count = 0;
    tbody.querySelectorAll("tr").forEach(r => {
      updateRow(r);
      const abs = parseInt((r.cells[14].textContent.match(/\d+/)||[0])[0]);
      const par = parseInt((r.cells[15].textContent.match(/\d+/)||[0])[0]);
      r.classList.remove("excellent");
      if (abs <= 2 && par >= 3) {
        r.classList.add("excellent");
        count++;
      }
    });
    showToast(`${count} étudiant(s) excellent(s) détecté(s) 🌟`);
  });

  // RESET COLORS
  resetBtn.addEventListener("click", () => {
    tbody.querySelectorAll("tr").forEach(r => {
      r.classList.remove("excellent","green","yellow","red","highlight");
      r.style.removeProperty("background");
      r.style.removeProperty("font-weight");
      r.style.removeProperty("color");
      updateRow(r);
    });
    showToast("Couleurs réinitialisées");
  });

  // EXERCICE 7: Recherche par nom
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const searchTerm = e.target.value.toLowerCase().trim();
      tbody.querySelectorAll("tr").forEach(row => {
        const lastName = row.cells[0].textContent.toLowerCase();
        const firstName = row.cells[1].textContent.toLowerCase();
        if (lastName.includes(searchTerm) || firstName.includes(searchTerm)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  }

  // EXERCICE 7: Tri par absences (croissant)
  if (sortAbsBtn) {
    sortAbsBtn.addEventListener("click", () => {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      rows.sort((a, b) => {
        const absA = parseInt((a.cells[14].textContent.match(/\d+/)||[0])[0]);
        const absB = parseInt((b.cells[14].textContent.match(/\d+/)||[0])[0]);
        return absA - absB;
      });
      rows.forEach(row => tbody.appendChild(row));
      if (sortStatus) sortStatus.textContent = "Trié par absences (croissant)";
      showToast("Trié par absences ⬆️");
    });
  }

  // EXERCICE 7: Tri par participations (décroissant)
  if (sortParBtn) {
    sortParBtn.addEventListener("click", () => {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      rows.sort((a, b) => {
        const parA = parseInt((a.cells[15].textContent.match(/\d+/)||[0])[0]);
        const parB = parseInt((b.cells[15].textContent.match(/\d+/)||[0])[0]);
        return parB - parA;
      });
      rows.forEach(row => tbody.appendChild(row));
      if (sortStatus) sortStatus.textContent = "Trié par participations (décroissant)";
      showToast("Trié par participations ⬇️");
    });
  }

  // RENDER REPORT (CORRIGÉ - suppression du code dupliqué)
  function renderReport() {
    const rows = tbody.querySelectorAll("tr");
    let total = rows.length;
    let presentCount = 0;
    let participatedCount = 0;

    rows.forEach(r => {
      const cells = r.querySelectorAll("td");
      let present = false;
      for (let i = 2; i <= 12; i += 2) {
        if (cells[i].textContent === "✓") present = true;
      }
      if (present) presentCount++;

      let hasParticipated = false;
      for (let i = 3; i <= 13; i += 2) {
        if (cells[i].textContent === "✓") hasParticipated = true;
      }
      if (hasParticipated) participatedCount++;
    });

    reportText.innerHTML = `
      <strong>Total étudiants :</strong> ${total} &nbsp;&nbsp;
      <strong>Présents (≥ 1 fois) :</strong> ${presentCount} &nbsp;&nbsp;
      <strong>Participants (≥ 1 fois) :</strong> ${participatedCount}
    `;

    const ctx = reportCanvas.getContext("2d");
    if (reportChart) reportChart.destroy();

    reportChart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Présents", "Total", "Participants"],
        datasets: [{
          data: [presentCount, total, participatedCount],
          backgroundColor: ["#ff77b0", "#9b59b6", "#f895cf"]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    });
  }

  if (document.querySelector(".nav-item.active")?.dataset.target === "reportSection") {
    renderReport();
  }

});
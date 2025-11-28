// Charger les étudiants depuis le backend
async function loadStudentsFromBackend() {
    console.log('🔄 Chargement des étudiants...');
    
    try {
        const result = await API.getStudents();
        console.log('📡 Réponse API reçue:', result);
        
        if (result.success && result.students) {
            console.log(`👥 ${result.students.length} étudiants à afficher`);
            
            // VIDER le tableau
            tbody.innerHTML = '';
            
            if (result.students.length > 0) {
                result.students.forEach((student, index) => {
                    console.log(`📝 Création ligne pour: ${student.firstName} ${student.lastName}`);
                    
                    const tr = document.createElement("tr");
                    
                    // Colonnes Nom et Prénom
                    let html = `<td>${student.lastName}</td>`;
                    html += `<td>${student.firstName}</td>`;
                    
                    // 6 sessions (P = Présence, Pa = Participation)
                    for (let i = 0; i < 6; i++) {
                        html += `<td class="clickable"></td>`; // Présence
                        html += `<td class="clickable"></td>`; // Participation
                    }
                    
                    // Colonnes statistiques
                    html += `<td class="absences-count">0 Abs</td>`;
                    html += `<td class="participations-count">0 Par</td>`;
                    html += `<td class="message-cell"></td>`;
                    
                    tr.innerHTML = html;
                    tbody.appendChild(tr);
                    
                    // Mettre à jour la ligne (couleurs, compteurs, message)
                    updateRow(tr);
                    
                    console.log(`✅ Ligne ${index + 1} ajoutée`);
                });
                
                showToast(`✅ ${result.students.length} étudiant(s) chargé(s)`);
            } else {
                console.log('ℹ️ Aucun étudiant dans le fichier');
                showToast('📝 Aucun étudiant trouvé');
                
                // Ajouter une ligne vide pour le style
                const tr = document.createElement("tr");
                tr.innerHTML = `<td colspan="17" style="text-align: center; padding: 20px; color: #666;">Aucun étudiant enregistré. Ajoutez des étudiants dans l'onglet "Ajouter".</td>`;
                tbody.appendChild(tr);
            }
        } else {
            console.error('❌ Erreur API:', result.message);
            showToast('❌ Erreur: ' + (result.message || 'Erreur inconnue'));
        }
    } catch (error) {
        console.error('💥 Erreur lors du chargement:', error);
        showToast('💥 Erreur de chargement');
    }
}

// Mettre à jour une ligne (couleurs, compteurs, message)
function updateRow(row) {
    const cells = row.querySelectorAll("td");
    if (!cells || cells.length < 17) return;
    
    let abs = 0, par = 0;
    
    // Compter les absences (colonnes paires : 2, 4, 6, 8, 10, 12)
    for (let i = 2; i <= 12; i += 2) {
        if (cells[i].textContent.trim() === "") abs++;
    }
    
    // Compter les participations (colonnes impaires : 3, 5, 7, 9, 11, 13)
    for (let i = 3; i <= 13; i += 2) {
        if (cells[i].textContent.trim() === "✓") par++;
    }
    
    // Mettre à jour les compteurs
    cells[14].textContent = abs + " Abs";
    cells[15].textContent = par + " Par";
    
    // Appliquer les couleurs
    row.classList.remove("green", "yellow", "red");
    if (abs < 3) row.classList.add("green");
    else if (abs <= 4) row.classList.add("yellow");
    else row.classList.add("red");

    // Message
    let msg = "";
    if (abs < 3 && par >= 3) msg = "Good attendance — Excellent participation";
    else if (abs >= 5) msg = "Excluded — too many absences";
    else if (abs < 3 && par < 3) msg = "Good attendance — You need to participate more";
    else if (abs >= 3 && par >= 3) msg = "Warning — low attendance but good participation";
    else msg = "Warning — low attendance and low participation";
    
    cells[16].textContent = msg;
}

// Application principale
document.addEventListener("DOMContentLoaded", () => {
    console.log('🚀 DOM chargé - Initialisation de l\'application');
    
    // Éléments principaux
    const navItems = document.querySelectorAll(".nav-item");
    const sections = document.querySelectorAll(".section");
    const toggleSidebarBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    
    const table = document.getElementById("attendanceTable");
    tbody = table.querySelector("tbody");
    
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
    const saveAttendanceBtn = document.getElementById("saveAttendance");
    
    // Exercice 7: Search and Sort
    const searchInput = document.getElementById("searchName");
    const sortAbsBtn = document.getElementById("sortByAbsences");
    const sortParBtn = document.getElementById("sortByParticipation");
    const sortStatus = document.getElementById("sortStatus");
    
    const reportText = document.getElementById("reportText");
    const reportCanvas = document.getElementById("reportChart");
    let reportChart = null;
    
    const toast = document.getElementById("toast");

    // Afficher un toast
    function showToast(msg) {
        if (!toast) {
            alert(msg);
            return;
        }
        toast.textContent = msg;
        toast.style.display = "block";
        toast.style.opacity = "1";
        
        if (toast._timeout) clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => { toast.style.display = "none"; }, 300);
        }, 3000);
    }

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

    // EVENT DELEGATION for clickable cells
    tbody.addEventListener("click", (ev) => {
        const td = ev.target.closest("td.clickable");
        if (!td) return;
        
        const isParticipation = (td.cellIndex % 2 === 1);
        const wasEmpty = td.textContent.trim() === "";
        td.textContent = wasEmpty ? "✓" : "";
        
        updateRow(td.parentElement);
        
        showToast(isParticipation ? 
            (wasEmpty ? "Participation ajoutée ✔️" : "Participation retirée ❌") :
            (wasEmpty ? "Présence ajoutée ✔️" : "Présence retirée ❌")
        );
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
        const participations = row.cells[15].textContent;
        
        alert(`📋 Fiche Étudiant :\n\n👤 ${firstName} ${lastName}\n${absences}\n${participations}`);
    });

    // FORMULAIRE AJOUT ÉTUDIANT
    studentForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        // Réinitialiser les erreurs
        idError.textContent = lastError.textContent = firstError.textContent = emailError.textContent = "";
        
        const id = studentId.value.trim();
        const last = lastName.value.trim();
        const first = firstName.value.trim();
        const email = emailField.value.trim();
        
        // Validation
        let valid = true;
        if (!/^[0-9]+$/.test(id)) { 
            idError.textContent = "ID invalide (chiffres seulement)"; 
            valid = false; 
        }
        if (!/^[A-Za-zÀ-ÿ\s\-]+$/.test(last)) { 
            lastError.textContent = "Nom invalide (lettres seulement)"; 
            valid = false; 
        }
        if (!/^[A-Za-zÀ-ÿ\s\-]+$/.test(first)) { 
            firstError.textContent = "Prénom invalide (lettres seulement)"; 
            valid = false; 
        }
        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { 
            emailError.textContent = "Email invalide"; 
            valid = false; 
        }
        
        if (!valid) return;
        
        const studentData = {
            student_id: id,
            lastName: last,
            firstName: first,
            email: email
        };
        
        console.log('📨 Envoi des données:', studentData);
        
        try {
            const result = await API.addStudent(studentData);
            console.log('📡 Réponse ajout étudiant:', result);
            
            if (result.success) {
                showToast("✅ Étudiant ajouté avec succès");
                studentForm.reset();
                
                // Recharger les étudiants IMMÉDIATEMENT
                await loadStudentsFromBackend();
            } else {
                if (result.errors) {
                    result.errors.forEach(err => {
                        showToast("❌ Erreur: " + err);
                    });
                } else {
                    showToast("❌ Erreur: " + (result.message || 'Erreur inconnue'));
                }
            }
        } catch (error) {
            console.error('💥 Erreur lors de l\'ajout:', error);
            showToast('💥 Erreur réseau');
        }
    });

    // HIGHLIGHT EXCELLENT
    highlightBtn.addEventListener("click", () => {
        let count = 0;
        tbody.querySelectorAll("tr").forEach(r => {
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
            r.classList.remove("excellent", "green", "yellow", "red", "highlight");
            r.style.removeProperty("background");
            r.style.removeProperty("font-weight");
            r.style.removeProperty("color");
            updateRow(r);
        });
        showToast("Couleurs réinitialisées");
    });

    // EXERCICE 7: Recherche par nom
    searchInput.addEventListener("input", (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        tbody.querySelectorAll("tr").forEach(row => {
            if (row.cells.length < 2) return;
            
            const rowLastName = row.cells[0].textContent.toLowerCase();
            const rowFirstName = row.cells[1].textContent.toLowerCase();
            
            if (rowLastName.includes(searchTerm) || rowFirstName.includes(searchTerm)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    // EXERCICE 7: Tri par absences (croissant)
    sortAbsBtn.addEventListener("click", () => {
        const rows = Array.from(tbody.querySelectorAll("tr:not([style*=\"display: none\"])"));
        rows.sort((a, b) => {
            const absA = parseInt((a.cells[14].textContent.match(/\d+/)||[0])[0]);
            const absB = parseInt((b.cells[15].textContent.match(/\d+/)||[0])[0]);
            return absA - absB;
        });
        
        rows.forEach(row => tbody.appendChild(row));
        sortStatus.textContent = "Trié par absences (croissant)";
        showToast("Trié par absences ⬆️");
    });

    // EXERCICE 7: Tri par participations (décroissant)
    sortParBtn.addEventListener("click", () => {
        const rows = Array.from(tbody.querySelectorAll("tr:not([style*=\"display: none\"])"));
        rows.sort((a, b) => {
            const parA = parseInt((a.cells[15].textContent.match(/\d+/)||[0])[0]);
            const parB = parseInt((b.cells[15].textContent.match(/\d+/)||[0])[0]);
            return parB - parA;
        });
        
        rows.forEach(row => tbody.appendChild(row));
        sortStatus.textContent = "Trié par participations (décroissant)";
        showToast("Trié par participations ⬇️");
    });

    // RENDER REPORT
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

    // SAUVEGARDER LES PRÉSENCES
    saveAttendanceBtn.addEventListener('click', async function() {
        const rows = tbody.querySelectorAll('tr');
        const attendanceData = [];
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 17) return;
            
            const student = {
                lastName: cells[0].textContent,
                firstName: cells[1].textContent,
                sessions: []
            };
            
            // Récupérer les données des 6 sessions
            for (let i = 2; i <= 13; i += 2) {
                student.sessions.push({
                    presence: cells[i].textContent === '✓',
                    participation: cells[i + 1].textContent === '✓'
                });
            }
            
            attendanceData.push(student);
        });
        
        console.log('💾 Sauvegarde des présences:', attendanceData);
        
        try {
            const result = await API.saveAttendance(attendanceData);
            
            if (result.success) {
                showToast('✅ Présences sauvegardées avec succès');
            } else {
                showToast('❌ Erreur sauvegarde: ' + result.message);
            }
        } catch (error) {
            console.error('💥 Erreur sauvegarde:', error);
            showToast('💥 Erreur de sauvegarde');
        }
    });

    // BOUTON RECHARGEMENT MANUEL
    const reloadBtn = document.createElement('button');
    reloadBtn.id = 'reloadStudents';
    reloadBtn.className = 'btn btn-primary';
    reloadBtn.innerHTML = '<i class="fa-solid fa-rotate"></i> Recharger les étudiants';
    
    // Ajouter le bouton dans les boutons d'action
    const buttonsRow = document.querySelector('.buttonsRow');
    if (buttonsRow) {
        buttonsRow.appendChild(reloadBtn);
        
        reloadBtn.addEventListener('click', async () => {
            await loadStudentsFromBackend();
        });
    }

    // CHARGEMENT INITIAL
    console.log('🎯 Début du chargement initial...');
    loadStudentsFromBackend();
});
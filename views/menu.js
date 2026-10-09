function toggleMenu() {
    const menu = document.getElementById("mobileMenu");

    if (menu) {
        const open = menu.classList.toggle("menu-open");
        const btn = document.querySelector(".menu-button");

        if (btn) {
            btn.setAttribute("aria-expanded", String(open));
        }
    }
}


// DISPONIBILITES : ID du médecin depuis le formulaire, pour patients ET soignants.

function chargerDisponibilitesRdv() {
    const formulaire = document.querySelector('.rdv-form-card');
    if (!formulaire || document.getElementById('creneaux-serveur')) return;
    const soignantId = formulaire.dataset.soignantId;
    const rdvId = formulaire.dataset.rdvId || '';
    const dateInput = formulaire.querySelector('input[name="date"]');
    if (!soignantId || !dateInput) return;



    // Seuls les créneaux validés par le serveur peuvent être sélectionnés.

    dateInput.readOnly = true;
    dateInput.placeholder = 'Choisissez un horaire ci-dessus';

    let zone = document.getElementById('disponibilites-zone');
    if (!zone) {
        zone = document.createElement('section');
        zone.id = 'disponibilites-zone';
        zone.className = 'card';
        formulaire.parentNode.insertBefore(zone, formulaire);
    }
    const titre = document.createElement('h3');
    titre.textContent = 'Horaires du soignant';
    zone.replaceChildren(titre);
    const chargement = document.createElement('p');
    chargement.textContent = 'Chargement des disponibilités…';
    zone.appendChild(chargement);

    fetch('index.php?route=rdv-disponibilites&soignant_id=' + encodeURIComponent(soignantId) + (rdvId ? '&rdv_id=' + encodeURIComponent(rdvId) : ''), {
        method: 'GET', credentials: 'same-origin', cache: 'no-store',
        headers: { 'Accept': 'application/json' }
    }).then(function(response) {
        if (!response.ok) throw new Error('Impossible de lire les horaires (' + response.status + ')');
        return response.json();
    }).then(function(creneaux) {
        chargement.remove();
        if (!Array.isArray(creneaux)) throw new Error('Format des disponibilités invalide');
        if (creneaux.length === 0) {
            const message = document.createElement('p');
            message.textContent = 'Aucun horaire proposé pour le moment.';
            zone.appendChild(message);
            return;
        }
        const legende = document.createElement('p');
        legende.textContent = 'Choisissez un horaire. Les horaires déjà pris sont grisés.';
        zone.appendChild(legende);
        const liste = document.createElement('div');
        liste.className = 'actions';
        creneaux.forEach(function(creneau) {
            const bouton = document.createElement('button');
            bouton.type = 'button';
            bouton.className = 'button';
            const libre = Number(creneau.disponible) === 1;
            bouton.textContent = creneau.date_heure_affichage + (creneau.creneau_actuel ? ' — Horaire actuel' : (libre ? '' : ' — Déjà pris'));
            bouton.disabled = !libre;
            if (!libre) {
                bouton.style.backgroundColor = '#999';
                bouton.style.color = '#fff';
                bouton.style.opacity = '0.65';
                bouton.style.cursor = 'not-allowed';
            } else {
                bouton.addEventListener('click', function() {
                    dateInput.value = creneau.date_heure_input;
                    dateInput.setCustomValidity('');
                    liste.querySelectorAll('button').forEach(function(b) { b.setAttribute('aria-pressed', 'false'); });
                    bouton.setAttribute('aria-pressed', 'true');
                    dateInput.focus();
                });
            }
            liste.appendChild(bouton);
        });
        zone.appendChild(liste);
    }).catch(function(err) {
        console.error(err);
        chargement.textContent = 'Impossible de charger les disponibilités. Vérifiez la connexion à MySQL, puis actualisez la page.';
    });
}

function configurerInscription() {
    const select = document.getElementById('role');
    const champs = document.getElementById('champs-soignant');
    const discipline = document.getElementById('id_discipline');
    if (!select || !champs || !discipline) return;
    function miseAJour() {
        const soignant = select.value === 'soignant';
        champs.style.display = soignant ? '' : 'none';
        discipline.required = soignant;
    }
    select.addEventListener('change', miseAJour);
    miseAJour();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { chargerDisponibilitesRdv(); configurerInscription(); });
} else {
    chargerDisponibilitesRdv();
    configurerInscription();
}

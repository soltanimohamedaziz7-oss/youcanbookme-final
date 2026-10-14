

// Je crée une fonction appelée toggleMenu().
// Elle permet d'ouvrir ou de fermer le menu de navigation.
function toggleMenu() {

    // Je recherche le menu grâce à son identifiant HTML mobileMenu.
    const menu = document.getElementById("mobileMenu");

    // Je vérifie que le menu existe avant de continuer.
    if (menu) {

        // classList.toggle() ajoute la classe menu-open si elle n'existe pas.
        // Si elle existe déjà, cette méthode la retire.
        // Cela permet d'ouvrir et de fermer le menu.
        const open = menu.classList.toggle("menu-open");

        // Je récupère le bouton qui permet d'ouvrir le menu.
        const btn = document.querySelector(".menu-button");

        // Je vérifie que le bouton existe.
        if (btn) {

            // Je mets à jour l'attribut aria-expanded.
            // Il indique aux outils d'accessibilité si le menu est ouvert.
            // String(open) transforme la valeur en texte : "true" ou "false".
            btn.setAttribute("aria-expanded", String(open));
        }
    }
}


// 
// 2. CHARGER LES DISPONIBILITÉS DES RENDEZ-VOUS
// 

// Cette fonction permet de récupérer les horaires proposés par un soignant.
// Elle permet ensuite à l'utilisateur de sélectionner un créneau disponible.
//
// Le JavaScript demande les disponibilités au serveur.
// C'est le serveur qui doit vérifier les données provenant de la base de données.

function chargerDisponibilitesRdv() {

    // Je recherche le formulaire de rendez-vous dans la page.
    const formulaire = document.querySelector('.rdv-form-card');

    // Si le formulaire n'existe pas, je quitte la fonction.
    // Je vérifie également si les créneaux sont déjà présents dans la page.
    if (!formulaire || document.getElementById('creneaux-serveur')) return;

    // Je récupère l'identifiant du soignant associé au formulaire.
    // dataset permet de lire les attributs HTML qui commencent par data-.
    const soignantId = formulaire.dataset.soignantId;

    // Je récupère l'identifiant du rendez-vous si le formulaire sert à modifier
    // un rendez-vous existant.
    // Si aucun identifiant n'existe, j'utilise une chaîne vide.
    const rdvId = formulaire.dataset.rdvId || '';

    // Je récupère le champ qui contient la date et l'heure du rendez-vous.
    const dateInput = formulaire.querySelector('input[name="date"]');

    // Si je n'ai pas l'identifiant du soignant ou le champ de date,
    // je quitte la fonction car je ne peux pas charger les disponibilités.
    if (!soignantId || !dateInput) return;



    // 
    // 2.1. PRÉPARER LE CHAMP DE DATE
    // 

    // Seuls les créneaux validés par le serveur peuvent être sélectionnés.

    // Je rends le champ de date non modifiable directement au clavier.
    // L'utilisateur doit choisir un horaire proposé dans la liste.
    dateInput.readOnly = true;

    // J'affiche un texte pour expliquer comment choisir la date.
    dateInput.placeholder = 'Choisissez un horaire ci-dessus';


    // 
    // 2.2. PRÉPARER LA ZONE DES DISPONIBILITÉS
    // 

    // Je recherche la zone qui doit afficher les disponibilités.
    let zone = document.getElementById('disponibilites-zone');

    // Si cette zone n'existe pas encore, je la crée.
    if (!zone) {

        // Je crée une nouvelle section HTML.
        zone = document.createElement('section');

        // Je lui donne un identifiant pour pouvoir la retrouver.
        zone.id = 'disponibilites-zone';

        // Je lui attribue la classe card pour reprendre le style du site.
        zone.className = 'card';

        // J'insère cette section juste avant le formulaire de rendez-vous.
        formulaire.parentNode.insertBefore(zone, formulaire);
    }

    // Je crée un titre pour la zone des disponibilités.
    const titre = document.createElement('h3');

    // Je définis le texte du titre.
    titre.textContent = 'Horaires du soignant';

    // Je remplace le contenu précédent de la zone par le nouveau titre.
    zone.replaceChildren(titre);

    // Je crée un paragraphe pour informer l'utilisateur du chargement.
    const chargement = document.createElement('p');

    // J'indique que les disponibilités sont en cours de chargement.
    chargement.textContent = 'Chargement des disponibilités…';

    // J'ajoute le message de chargement dans la zone.
    zone.appendChild(chargement);


    // 
    // 2.3. DEMANDER LES DISPONIBILITÉS AU SERVEUR
    // 

    // fetch() permet d'envoyer une demande HTTP au serveur.
    // Ici, je demande les disponibilités du soignant sélectionné.
    //
    // encodeURIComponent() prépare les valeurs pour leur transmission dans l'URL.
    // Si je modifie un rendez-vous, je transmets aussi son identifiant.
    fetch('index.php?route=rdv-disponibilites&soignant_id=' + encodeURIComponent(soignantId) + (rdvId ? '&rdv_id=' + encodeURIComponent(rdvId) : ''), {

        // GET permet de demander des informations au serveur.
        method: 'GET',

        // J'envoie les informations de session pour que la demande
        // puisse être traitée dans le contexte de l'utilisateur connecté.
        credentials: 'same-origin',

        // Je demande au navigateur de ne pas réutiliser une ancienne réponse en cache.
        cache: 'no-store',

        // J'indique que je souhaite recevoir une réponse au format JSON.
        headers: { 'Accept': 'application/json' }

    // 
    // 2.4. VÉRIFIER LA RÉPONSE DU SERVEUR
    // 

    // then() permet de traiter la réponse lorsque la demande est terminée.
    }).then(function(response) {

        // Je vérifie si la réponse du serveur indique une réussite.
        // Si ce n'est pas le cas, je déclenche une erreur.
        if (!response.ok) throw new Error('Impossible de lire les horaires (' + response.status + ')');

        // Je transforme la réponse JSON en données JavaScript utilisables.
        return response.json();

    // 
    // 2.5. AFFICHER LES CRÉNEAUX REÇUS
    // 

    // Cette fonction reçoit les créneaux retournés par le serveur.
    }).then(function(creneaux) {

        // Je retire le message de chargement.
        chargement.remove();

        // Je vérifie que la réponse est bien une liste.
        // Array.isArray() permet de vérifier si une valeur est un tableau.
        if (!Array.isArray(creneaux)) throw new Error('Format des disponibilités invalide');

        // Si la liste est vide, aucun créneau n'est proposé.
        if (creneaux.length === 0) {

            // Je crée un paragraphe pour informer l'utilisateur.
            const message = document.createElement('p');

            // J'indique qu'aucun horaire n'est disponible pour le moment.
            message.textContent = 'Aucun horaire proposé pour le moment.';

            // J'ajoute le message dans la zone des disponibilités.
            zone.appendChild(message);

            // Je quitte cette fonction de traitement pour ce résultat.
            return;
        }

        // Si des créneaux existent, j'explique à l'utilisateur comment les choisir.
        const legende = document.createElement('p');
        legende.textContent = 'Choisissez un horaire. Les horaires déjà pris sont grisés.';

        // J'ajoute la légende dans la zone.
        zone.appendChild(legende);

        // Je crée un conteneur pour regrouper les boutons des créneaux.
        const liste = document.createElement('div');

        // Je réutilise la classe actions pour appliquer le style prévu dans le CSS.
        liste.className = 'actions';

        // Je parcours chaque créneau reçu du serveur.
        // À chaque passage, la variable creneau représente un horaire.
        creneaux.forEach(function(creneau) {

            // Je crée un bouton pour chaque horaire.
            const bouton = document.createElement('button');

            // Le bouton ne doit pas envoyer directement le formulaire.
            bouton.type = 'button';

            // Je lui applique la classe CSS button.
            bouton.className = 'button';

            // Je vérifie si le créneau est disponible.
            // Number() transforme la valeur en nombre.
            const libre = Number(creneau.disponible) === 1;

            // J'affiche la date et l'heure du créneau.
            // Si c'est le rendez-vous actuellement modifié, je le précise.
            // Sinon, si le créneau est déjà pris, je l'indique également.
            bouton.textContent = creneau.date_heure_affichage + (creneau.creneau_actuel ? ' — Horaire actuel' : (libre ? '' : ' — Déjà pris'));

            // Si le créneau n'est pas disponible, je désactive le bouton.
            bouton.disabled = !libre;

            // 
            // 2.6. AFFICHER LES CRÉNEAUX INDISPONIBLES
            // 

            if (!libre) {

                // Je rends le bouton gris pour montrer que le créneau est indisponible.
                bouton.style.backgroundColor = '#999';

                // Je mets le texte en blanc pour qu'il reste lisible.
                bouton.style.color = '#fff';

                // Je réduis légèrement l'opacité du bouton.
                bouton.style.opacity = '0.65';

                // Je change le curseur pour indiquer que le bouton ne peut pas être utilisé.
                bouton.style.cursor = 'not-allowed';

            } else {

                // 
                // 2.7. SÉLECTIONNER UN CRÉNEAU DISPONIBLE
                // 

                // J'ajoute une action lorsque l'utilisateur clique sur un créneau libre.
                bouton.addEventListener('click', function() {

                    // Je place la date et l'heure choisies dans le champ du formulaire.
                    dateInput.value = creneau.date_heure_input;

                    // J'efface un éventuel message de validation personnalisé.
                    dateInput.setCustomValidity('');

                    // Je retire l'état sélectionné de tous les boutons.
                    // Cela permet de montrer qu'un seul créneau est sélectionné.
                    liste.querySelectorAll('button').forEach(function(b) {
                        b.setAttribute('aria-pressed', 'false');
                    });

                    // J'indique que le bouton cliqué est celui qui est sélectionné.
                    bouton.setAttribute('aria-pressed', 'true');

                    // Je replace le curseur sur le champ de date du formulaire.
                    dateInput.focus();
                });
            }

            // J'ajoute le bouton à la liste des créneaux.
            liste.appendChild(bouton);
        });

        // Lorsque tous les boutons sont créés, j'affiche la liste dans la page.
        zone.appendChild(liste);

    // 
    // 2.8. GÉRER LES ERREURS
    // 

    // catch() permet de récupérer une erreur survenue pendant la demande
    // ou pendant le traitement de la réponse.
    }).catch(function(err) {

        // J'affiche les détails de l'erreur dans la console du navigateur.
        // Cela m'aide à rechercher l'origine du problème pendant le développement.
        console.error(err);

        // J'affiche un message dans la page pour informer l'utilisateur.
        chargement.textContent = 'Impossible de charger les disponibilités. Vérifiez la connexion à MySQL, puis actualisez la page.';
    });
}


// 
// 3. AFFICHER LES CHAMPS SELON LE TYPE DE COMPTE
// 

// Cette fonction gère les champs supplémentaires du formulaire d'inscription.
// Si l'utilisateur choisit le compte soignant, les champs professionnels apparaissent.
function configurerInscription() {

    // Je récupère la liste qui permet de choisir le type de compte.
    const select = document.getElementById('role');

    // Je récupère le bloc qui contient les champs propres au soignant.
    const champs = document.getElementById('champs-soignant');

    // Je récupère la liste des spécialités.
    const discipline = document.getElementById('id_discipline');

    // Si un de ces éléments n'existe pas sur la page,
    // je quitte la fonction sans effectuer de modification.
    if (!select || !champs || !discipline) return;

    // Je crée une fonction qui adapte l'affichage au type de compte choisi.
    function miseAJour() {

        // Je vérifie si le visiteur a choisi le type de compte soignant.
        const soignant = select.value === 'soignant';

        // Si c'est un soignant, j'affiche le bloc.
        // Sinon, je le masque.
        champs.style.display = soignant ? '' : 'none';

        // La spécialité devient obligatoire uniquement pour un soignant.
        discipline.required = soignant;
    }

    // J'écoute les changements de choix dans la liste.
    // Quand le visiteur change de type de compte, je mets à jour l'affichage.
    select.addEventListener('change', miseAJour);

    // J'appelle la fonction une première fois pour adapter les champs
    // au choix déjà présent dans la liste.
    miseAJour();
}


// 
// 4. LANCER LE JAVASCRIPT AU BON MOMENT
// 

// document.readyState indique l'état de chargement du document HTML.

// Si la page est encore en cours de chargement...
if (document.readyState === 'loading') {

    // ...j'attends que le HTML soit chargé avant d'exécuter les fonctions.
    document.addEventListener('DOMContentLoaded', function() {

        // Je charge les disponibilités du rendez-vous si le formulaire existe.
        chargerDisponibilitesRdv();

        // Je configure les champs supplémentaires de l'inscription si nécessaire.
        configurerInscription();
    });

} else {

    // Si le HTML est déjà chargé, je lance directement les deux fonctions.
    chargerDisponibilitesRdv();
    configurerInscription();
}
<?php
// Le routeur vérifie la connexion avant de lancer les contrôleurs.



// ROUTEUR PRINCIPAL




// 
// Toutes les pages passent par ce fichier.
// Le switch permet de choisir la bonne action.
// 

require_once __DIR__ . "/config/config.php";

require_once __DIR__ . "/controllers/auth.php";
require_once __DIR__ . "/controllers/home.php";
require_once __DIR__ . "/controllers/soignant.php";
require_once __DIR__ . "/controllers/rdv.php";




// Route demandée dans l'URL.
$route = $_GET["route"] ?? "login";



// Routes qui nécessitent une connexion.
$private_routes = [
    "dashboard",
    "profil",
    "soignants",
    "soignants-search",
    "mes-soignants",
    "favori-toggle",
    "soignant-add",
    "soignant-detail",
    "soignant-edit",
    "soignant-delete",
    "rdvs",
    "mes-rdvs-soignant",
    "rdv-add",
    "rdv-disponibilites",
    "rdv-delete",
    "rdv-edit"
];

// Si l'utilisateur n'est pas connecté,
// il ne peut pas accéder aux pages privées.
if (
    in_array($route, $private_routes)
    && empty($_SESSION["user_id"])
) {
    header("Location: index.php?route=login");
    exit;
}

if (in_array($route, ['mes-soignants','favori-toggle','soignants','soignants-search','soignant-detail'], true) && is_soignant()) {
    http_response_code(403); exit('Action réservée aux patients.');
}



// Seule l'inscription crée un nouveau compte soignant. Jamais une session patient.
// Bloquer ces routes AVANT l'appel au contrôleur, y compris en URL directe.

if ($route === 'soignant-add' || $route === 'soignant-delete') {
    http_response_code(403);
    exit('La création d’un compte soignant se fait uniquement à l’inscription ; la suppression de comptes est désactivée.');
}


// Le switch choisit l'action à exécuter.
switch ($route) {

    case "login":
        login();
        break;

    case "register":
        register();
        break;

    case "logout":
        logout();
        break;

    case "dashboard":
        dashboard();
        break;

    case "profil":
        profil();
        break;

    case "soignants":
        soignants();
        break;

    case "soignants-search":
        soignant_search();
        break;

    case "mes-soignants":
        mes_favoris();
        break;

    case "favori-toggle":
        toggle_favori();
        break;

    case "soignant-add":
        soignant_add();
        break;

    case "soignant-detail":
        soignant_detail();
        break;

    case "soignant-edit":
        soignant_edit();
        break;

    case "soignant-delete":
        http_response_code(403); exit("Suppression des soignants désactivée.");
        break;

    case "rdvs":
        rdvs();
        break;

    case "mes-rdvs-soignant":
        if (!is_soignant()) { http_response_code(403); exit('Réservé aux soignants.'); }
        rdvs();
        break;

    case "rdv-add":
        rdv_add();
        break;

    case "rdv-disponibilites":
        rdv_disponibilites();
        break;

    case "rdv-edit":
        rdv_edit();
        break;

    case "rdv-delete":
        rdv_delete();
        break;

    default:

    
        // Route inconnue = erreur 404.
        http_response_code(404);

        require __DIR__ . "/views/html/404.php";
        break;
}

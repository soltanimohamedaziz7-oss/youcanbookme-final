<?php
// Ce contrôleur vérifie le rôle, le jeton CSRF et le rendez-vous visé avant toute modification.
require_once __DIR__ . '/../models/rdv.php';
require_once __DIR__ . '/../models/soignant.php';

function rdvs()
{
    $notification = $_SESSION['rdv_notification'] ?? null;
    unset($_SESSION['rdv_notification']);
    if (is_soignant()) {
        $rdvs = get_rdvs_by_soignant(get_owned_soignant_id((int)$_SESSION['user_id']));
    } else {
        $rdvs = get_rdvs_by_user((int)$_SESSION['user_id']);
    }
    require __DIR__ . '/../views/html/rdvs.php';
}

function rdv_add()
{
    $user_id = (int)$_SESSION['user_id'];
    $is_provider = is_soignant();
    $recherche = trim($_GET['recherche'] ?? '');
    $recherche_patient = trim($_GET['patient'] ?? '');
    $patients = [];
    $soignants = [];
    $soignant_selectionne = (int)($_GET['soignant_id'] ?? 0);
    $soignant_choisi = null;

    if ($is_provider) {
        $soignant_selectionne = get_owned_soignant_id($user_id);
        if (!$soignant_selectionne) { http_response_code(403); exit('Profil soignant introuvable.'); }
        if ($recherche_patient !== '') $patients = find_patients($recherche_patient);
    }

    if ($soignant_selectionne > 0) {
        $soignant_choisi = get_soignant_by_id($soignant_selectionne, $user_id);
        if (!$soignant_choisi) {
            http_response_code(404);
            $error = 'Ce soignant est introuvable. Sélectionnez un autre soignant.';
            $soignant_selectionne = 0;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $id_soignant = (int)($_POST['id_soignant'] ?? 0);
        $patient_id = $is_provider ? (int)($_POST['id_user'] ?? 0) : $user_id;
        $date = trim($_POST['date'] ?? '');
        $description = trim($_POST['description'] ?? '');
        // Si le médecin de l'URL et du POST diffèrent, refuser toute réservation.
        if (!$soignant_choisi || !$id_soignant || $id_soignant !== $soignant_selectionne) {
            $error = 'Le soignant sélectionné ne correspond pas au formulaire.';
        } elseif (!$date || ($is_provider && !$patient_id)) {
            $error = 'Choisissez un patient et un horaire disponible.';
        } elseif (create_rdv($id_soignant, $patient_id, $date, $description)) {
            header('Location: index.php?route=rdvs');
            exit;
        } else {
            $error = 'Ce créneau est déjà réservé ou invalide. Choisissez un autre horaire libre.';
        }
    }

    // Ne montrer la liste que lorsqu'aucun soignant n'est sélectionné.
    // Sinon, afficher directement la réservation en haut de page.
    if (!$is_provider && !$soignant_choisi) {
        $soignants = $recherche !== '' ? search_soignants($user_id, $recherche) : get_soignants_by_user($user_id);
    }

    $creneaux_page = [];
    if ($soignant_choisi) {
        if (ensure_disponibilites($soignant_selectionne)) {
            $creneaux_page = get_disponibilites($soignant_selectionne);
        } else {
            $error = 'Impossible de charger les créneaux de ce soignant.';
        }
    }
    require __DIR__ . '/../views/html/add-rdv.php';
}

function rdv_disponibilites()
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    $sid = (int)($_GET['soignant_id'] ?? 0);
    $rdv_id = (int)($_GET['rdv_id'] ?? 0);
    if (is_soignant() && $sid !== get_owned_soignant_id((int)$_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode([]);
        exit;
    }
    if (!$sid || !ensure_disponibilites($sid)) {
        echo json_encode([]);
        exit;
    }
    // Une édition autorisée peut reprendre son créneau actuel, sans débloquer les autres.
    $mine = $rdv_id ? find_authorized_rdv($rdv_id, (int)$_SESSION['user_id'], current_role()) : null;
    $creneaux = get_disponibilites($sid);
    if ($mine && (int)$mine['id_soignant'] === $sid) {
        foreach ($creneaux as &$creneau) {
            if ($creneau['date_heure'] === $mine['date']) {
                $creneau['disponible'] = 1;
                $creneau['creneau_actuel'] = true;
            }
        }
        unset($creneau);
    }
    echo json_encode($creneaux, JSON_UNESCAPED_UNICODE);
    exit;
}

function rdv_delete()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?route=rdvs');
        exit;
    }
    verify_csrf();
    // L'identifiant vient du formulaire du rendez-vous cliqué, jamais de l'URL.
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $ok = $id !== false && $id !== null && delete_rdv((int)$id, (int)$_SESSION['user_id'], current_role());
    $_SESSION['rdv_notification'] = $ok
        ? ['type' => 'success', 'message' => 'Le rendez-vous a été annulé et le créneau est à nouveau disponible.']
        : ['type' => 'error', 'message' => 'Annulation impossible : rendez-vous introuvable, non autorisé ou erreur de base de données.'];
    header('Location: index.php?route=rdvs');
    exit;
}

// Patient : ses rendez-vous uniquement. Soignant : rendez-vous de son agenda uniquement.
function rdv_edit()
{
    $id = (int)($_GET['id'] ?? 0);
    $account_id = (int)$_SESSION['user_id'];
    $role = current_role();
    $rdv = find_authorized_rdv($id, $account_id, $role);
    if (!$rdv) {
        http_response_code(404);
        exit('Rendez-vous introuvable ou accès refusé.');
    }
    $soignant_selectionne = (int)$rdv['id_soignant'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        if (update_rdv_by_role($id, $account_id, $role, $_POST['date'] ?? '', trim($_POST['description'] ?? ''))) {
            header('Location: index.php?route=rdvs');
            exit;
        }
        $error = 'Impossible de modifier : choisissez un créneau libre à venir. Votre rendez-vous initial est conservé.';
    }
    require __DIR__ . '/../views/html/edit-rdv.php';
}

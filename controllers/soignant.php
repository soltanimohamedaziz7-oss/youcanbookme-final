<?php
// Ce contrôleur traite les demandes et vérifie les autorisations avant de modifier un soignant.
// ============================================================
// CONTROLEUR SOIGNANTS
// ============================================================
// Le controller reçoit les demandes de l'utilisateur.
// Ensuite il appelle le model pour travailler avec la BDD.
// ============================================================

require_once __DIR__ . "/../models/soignant.php";

// Afficher la liste des soignants.
function soignants()
{
    $user_id = $_SESSION["user_id"];

    $soignants = get_soignants_by_user($user_id);

    require __DIR__ . "/../views/html/soignants.php";
}

// Rechercher un soignant par nom, prénom ou discipline.
function soignant_search()
{
    $user_id = $_SESSION["user_id"];

    $recherche = trim($_GET["recherche"] ?? "");

    $soignants = search_soignants(
        $user_id,
        $recherche
    );

    require __DIR__ . "/../views/html/soignants.php";
}

// Afficher uniquement les favoris de l'utilisateur.
function mes_favoris()
{
    $user_id = $_SESSION["user_id"];

    $favoris = get_favoris_by_user($user_id);

    require __DIR__ . "/../views/html/mes-soignants.php";
}

// Ajouter ou retirer un soignant des favoris.
function toggle_favori()
{
    // Cette action doit être faite en POST.
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php?route=soignants");
        exit;
    }

    // Vérification CSRF.
    verify_csrf();

    $user_id = $_SESSION["user_id"];

    $soignant_id = (int) (
        $_POST["soignant_id"] ?? 0
    );

    if ($soignant_id <= 0) {
        header("Location: index.php?route=soignants");
        exit;
    }

    // On récupère les favoris actuels.
    $favoris = get_favoris_by_user($user_id);

    $est_favori = false;

    foreach ($favoris as $favori) {
        if ((int) $favori["id"] === $soignant_id) {
            $est_favori = true;
            break;
        }
    }

    // Si le coeur est déjà rouge, on retire le favori.
    if ($est_favori) {
        remove_favori(
            $user_id,
            $soignant_id
        );
    }
    // Sinon, on ajoute le favori.
    else {
        add_favori(
            $user_id,
            $soignant_id
        );
    }

    // Retour à la liste.
    header("Location: index.php?route=soignants");
    exit;
}

function soignant_add()
{
    $user_id = $_SESSION["user_id"];
    $disciplines = get_disciplines();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        verify_csrf();

        $nom = trim($_POST["nom"] ?? "");
        $prenom = trim($_POST["prenom"] ?? "");
        $date_naissance = $_POST["date_naissance"] ?? "";
        $adresse = trim($_POST["adresse"] ?? "");
        $mail = trim($_POST["mail"] ?? "");
        $discipline_id = (int) ($_POST["id_discipline"] ?? 0);

        if (
            $nom === "" ||
            $prenom === "" ||
            $date_naissance === ""
        ) {
            $error = "Nom, prénom et date de naissance sont obligatoires.";
        } else {
            create_soignant(
                $nom,
                $prenom,
                $date_naissance,
                $adresse,
                $mail,
                $discipline_id,
                $user_id
            );

            header("Location: index.php?route=soignants");
            exit;
        }
    }

    require __DIR__ . "/../views/html/add-soignant.php";
}

function soignant_detail()
{
    $user_id = $_SESSION["user_id"];
    $id = (int) ($_GET["id"] ?? 0);

    $soignant = get_soignant_by_id($id, $user_id);

    if (!$soignant) {
        http_response_code(404);
        require __DIR__ . "/../views/html/404.php";
        return;
    }

    require __DIR__ . "/../views/html/detail-soignant.php";
}

function soignant_edit()
{
    $user_id = $_SESSION["user_id"];
    $id = (int) ($_GET["id"] ?? 0);

    $soignant = get_soignant_by_id($id, $user_id);

    // Vérification d'autorisation CÔTÉ SERVEUR : un soignant ne peut
    // modifier QUE sa propre fiche. Un patient ne peut modifier aucune fiche.
    if (!is_soignant() || !$soignant || (int)$soignant['id_user'] !== (int)$user_id) {
        http_response_code(403);
        exit('Vous ne pouvez modifier que votre propre fiche soignant.');
    }

    $disciplines = get_disciplines();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        verify_csrf();

        $nom = trim($_POST["nom"] ?? "");
        $prenom = trim($_POST["prenom"] ?? "");
        $date_naissance = $_POST["date_naissance"] ?? "";
        $adresse = trim($_POST["adresse"] ?? "");
        $mail = trim($_POST["mail"] ?? "");
        $discipline_id = (int) ($_POST["id_discipline"] ?? 0);

        update_soignant(
            $id,
            $nom,
            $prenom,
            $date_naissance,
            $adresse,
            $mail,
            $discipline_id,
            $user_id
        );

        header("Location: index.php?route=soignant-detail&id=" . $id);
        exit;
    }

    require __DIR__ . "/../views/html/edit-soignant.php";
}

function soignant_delete()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php?route=soignants");
        exit;
    }

    verify_csrf();

    $id = (int) ($_POST["id"] ?? 0);
    $user_id = $_SESSION["user_id"];

    delete_soignant($id, $user_id);

    header("Location: index.php?route=soignants");
    exit;
}

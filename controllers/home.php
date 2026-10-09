<?php
// Ce contrôleur gère les pages privées et les données du profil.
// ============================================================
// CONTROLEUR ACCUEIL ET PROFIL
// ============================================================

require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../models/soignant.php";

function dashboard()
{
    require __DIR__ . "/../views/html/dashboard.php";
}

// Afficher et modifier le profil.
function profil()
{
    $user_id = $_SESSION["user_id"];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        verify_csrf();

        $nom = trim($_POST["nom"] ?? "");
        $prenom = trim($_POST["prenom"] ?? "");
        $date_naissance = $_POST["date_naissance"] ?? "";

        if (
            $nom === "" ||
            $prenom === "" ||
            $date_naissance === ""
        ) {
            $error = "Veuillez remplir les trois champs.";
        } else {
            // Le profil ne modifie QUE :
            // nom, prénom et date de naissance.
            update_user(
                $user_id,
                $nom,
                $prenom,
                $date_naissance
            );
            if (is_soignant()) {
                sync_own_soignant_identity((int)$user_id, $nom, $prenom, $date_naissance);
            }

            $_SESSION["user_nom"] = $nom;
            $_SESSION["user_prenom"] = $prenom;

            $success = "Votre profil a été modifié.";
        }
    }

    $user = find_user_by_id($user_id);

    require __DIR__ . "/../views/html/profil.php";
}

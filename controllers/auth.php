<?php
// Ce contrôleur vérifie les mots de passe hachés et crée les sessions après connexion.
// ============================================================
// CONTROLEUR AUTHENTIFICATION
// Login, inscription et déconnexion.
// ============================================================

require_once __DIR__ . "/../models/user.php";

// Afficher la page de connexion.
function login()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        verify_csrf();

        $mail = trim($_POST["mail"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($mail === "" || $password === "") {
            $error = "Veuillez remplir tous les champs.";
            require __DIR__ . "/../views/html/login.php";
            return;
        }

        $user = find_user_by_mail($mail);

        if (!$user || !password_verify($password, $user["password"])) {
            $error = "Email ou mot de passe incorrect.";
            require __DIR__ . "/../views/html/login.php";
            return;
        }

        // On change l'identifiant de session après connexion.
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_nom"] = $user["nom"];
        $_SESSION["user_prenom"] = $user["prenom"];
        $_SESSION["role"] = $user["role"] ?? "patient";

        header("Location: index.php?route=dashboard");
        exit;
    }

    require __DIR__ . "/../views/html/login.php";
}

// Inscription : le rôle est choisi à la création du compte et ne peut pas être changé.
// Création du compte et de la fiche médicale dans UNE transaction atomique.
function register()
{
    require_once __DIR__ . '/../models/soignant.php';
    $disciplines = get_disciplines();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $date_naissance = trim($_POST['date_naissance'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $discipline_id = (int)($_POST['id_discipline'] ?? 0);
        $adresse = trim($_POST['adresse'] ?? '');
        if (!$nom || !$prenom || !$date_naissance || !$mail || !$password || !in_array($role, ['patient','soignant'], true)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif (!DateTimeImmutable::createFromFormat('!Y-m-d', $date_naissance)
            || DateTimeImmutable::createFromFormat('!Y-m-d', $date_naissance)->format('Y-m-d') !== $date_naissance) {
            $error = 'La date de naissance est invalide.';
        } elseif ($role === 'soignant' && !in_array($discipline_id, array_map(static fn($d) => (int)$d['id'], $disciplines), true)) {
            $error = 'Veuillez choisir une spécialité pour votre compte soignant.';
        } elseif (find_user_by_mail($mail)) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            $db = open_connection();
            mysqli_begin_transaction($db);
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $q = mysqli_prepare($db, 'INSERT INTO users (nom,prenom,date_naissance,mail,password,role) VALUES(?,?,?,?,?,?)');
                mysqli_stmt_bind_param($q, 'ssssss', $nom, $prenom, $date_naissance, $mail, $hash, $role);
                mysqli_stmt_execute($q);
                mysqli_stmt_close($q);
                $uid = mysqli_insert_id($db);
                if ($role === 'soignant') {
                    $q = mysqli_prepare($db, 'INSERT INTO soignants (nom,prenom,date_naissance,adresse,mail,id_discipline,id_user) VALUES (?,?,?,?,?,?,?)');
                    mysqli_stmt_bind_param($q, 'sssssii', $nom, $prenom, $date_naissance, $adresse, $mail, $discipline_id, $uid);
                    mysqli_stmt_execute($q);
                    mysqli_stmt_close($q);
                }
                mysqli_commit($db);
                close_connection($db);
                header('Location: index.php?route=login');
                exit;
            } catch (Throwable $exception) {
                mysqli_rollback($db);
                close_connection($db);
                $error = 'Impossible de créer le compte. Vérifiez les informations et réessayez.';
            }
        }
    }
    require __DIR__ . '/../views/html/register.php';
}

// Déconnexion.
function logout()
{
    $_SESSION = [];

    session_destroy();

    header("Location: index.php?route=login");
    exit;
}

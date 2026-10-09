<?php
// Les fonctions de ce fichier lisent et modifient les comptes avec des paramètres SQL séparés.




// MODELE USER
// Toutes les requêtes concernant les utilisateurs sont ici.
// 

require_once __DIR__ . "/database.php";

// Création d'un utilisateur.
function create_user($nom, $prenom, $date_naissance, $mail, $password_hash, $role = "patient")
{
    $connection = open_connection();

    $sql = "INSERT INTO users
            (nom, prenom, date_naissance, mail, password, role)
            VALUES (?, ?, ?, ?, ?, ?)";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "ssssss",
        $nom,
        $prenom,
        $date_naissance,
        $mail,
        $password_hash,
        $role
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// Recherche un utilisateur avec son adresse mail.
function find_user_by_mail($mail)
{
    $connection = open_connection();

    $sql = "SELECT
                id,
                nom,
                prenom,
                date_naissance,
                mail,
                password,
                role,
                created_at
            FROM users
            WHERE mail = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param($request, "s", $mail);

    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $user;
}

// Recherche un utilisateur avec son ID.
function find_user_by_id($id)
{
    $connection = open_connection();

    $sql = "SELECT
                id,
                nom,
                prenom,
                date_naissance,
                mail,
                role,
                created_at
            FROM users
            WHERE id = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param($request, "i", $id);

    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $user;
}

// Modification du profil.
// Seuls nom, prénom et date de naissance sont modifiés.
function update_user($id, $nom, $prenom, $date_naissance)
{
    $connection = open_connection();

    $sql = "UPDATE users
            SET nom = ?,
                prenom = ?,
                date_naissance = ?
            WHERE id = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "sssi",
        $nom,
        $prenom,
        $date_naissance,
        $id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

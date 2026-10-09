<?php
// Les données des soignants sont consultées avec MySQLi et des requêtes préparées


// 
// MODELE SOIGNANT


// 
// Ce fichier contient les requêtes SQL concernant les soignants.


// On utilise mysqli et les requêtes préparées.


// 

require_once __DIR__ . "/database.php";

// Retourne les disciplines disponibles.
function get_disciplines()
{
    $connection = open_connection();

    $sql = "SELECT id, nom
            FROM disciplines
            ORDER BY nom ASC";

    $request = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($request);
    $result = mysqli_stmt_get_result($request);

    $disciplines = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $disciplines[] = $row;
    }

    close_connection($connection);

    // Fermer la requête préparée pour libérer les ressources.
    mysqli_stmt_close($request);

    return $disciplines;
}

// Liste les soignants disponibles dans l'application.
// Le favori reste propre à l'utilisateur connecté.
function get_soignants_by_user($user_id)
{
    $connection = open_connection();
    // commentaire

    $sql = "SELECT
                s.id,
                s.id_user,
                s.nom,
                s.prenom,
                s.date_naissance,
                s.adresse,
                s.mail,
                s.id_discipline,
                d.nom AS discipline,
                f.id AS favori_id
            FROM soignants s
            LEFT JOIN disciplines d
                ON s.id_discipline = d.id
            LEFT JOIN favoris f
                ON f.id_soignant = s.id
                AND f.id_user = ?
            ORDER BY s.nom ASC, s.prenom ASC";

    $request = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($request, "i", $user_id);
    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);
    $soignants = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $soignants[] = $row;
    }

    mysqli_stmt_close($request);
    close_connection($connection);

    return $soignants;
}

// Recherche un soignant disponible dans l'application.
function get_soignant_by_id($id, $user_id)
{
    $connection = open_connection();

    $sql = "SELECT
                s.id,
                s.id_user,
                s.nom,
                s.prenom,
                s.date_naissance,
                s.adresse,
                s.mail,
                s.id_discipline,
                d.nom AS discipline
            FROM soignants s
            LEFT JOIN disciplines d
                ON s.id_discipline = d.id
            WHERE s.id = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "i",
        $id
    );

    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);
    $soignant = mysqli_fetch_assoc($result);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $soignant;
}

// Recherche par nom, prénom ou discipline.
function search_soignants($user_id, $recherche)
{
    $connection = open_connection();

    $recherche = "%" . $recherche . "%";

    $sql = "SELECT
                s.id,
                s.id_user,
                s.nom,
                s.prenom,
                s.date_naissance,
                s.adresse,
                s.mail,
                s.id_discipline,
                d.nom AS discipline,
                f.id AS favori_id
            FROM soignants s
            LEFT JOIN disciplines d
                ON s.id_discipline = d.id
            LEFT JOIN favoris f
                ON f.id_soignant = s.id
                AND f.id_user = ?
            WHERE (
                s.nom LIKE ?
                OR s.prenom LIKE ?
                OR d.nom LIKE ?
            )
            ORDER BY s.nom ASC, s.prenom ASC";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "isss",
        $user_id,
        $recherche,
        $recherche,
        $recherche
    );

    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);
    $soignants = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $soignants[] = $row;
    }

    mysqli_stmt_close($request);
    close_connection($connection);

    return $soignants;
}

// Ajouter un soignant.
function create_soignant(
    $nom,
    $prenom,
    $date_naissance,
    $adresse,
    $mail,
    $discipline_id,
    $user_id
)
{
    $connection = open_connection();

    $sql = "INSERT INTO soignants
            (
                nom,
                prenom,
                date_naissance,
                adresse,
                mail,
                id_discipline,
                id_user
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "sssssii",
        $nom,
        $prenom,
        $date_naissance,
        $adresse,
        $mail,
        $discipline_id,
        $user_id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// Modifier un soignant.
function update_soignant(
    $id,
    $nom,
    $prenom,
    $date_naissance,
    $adresse,
    $mail,
    $discipline_id,
    $user_id
)
{
    $connection = open_connection();

    $sql = "UPDATE soignants
            SET nom = ?,
                prenom = ?,
                date_naissance = ?,
                adresse = ?,
                mail = ?,
                id_discipline = ?
            WHERE id = ?
            AND id_user = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "sssssiii",
        $nom,
        $prenom,
        $date_naissance,
        $adresse,
        $mail,
        $discipline_id,
        $id,
        $user_id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// Supprimer un soignant.
function delete_soignant($id, $user_id)
{
    $connection = open_connection();

    $sql = "DELETE FROM soignants
            WHERE id = ?
            AND id_user = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "ii",
        $id,
        $user_id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// 
// FAVORIS
// 

// Ajoute un soignant aux favoris de l'utilisateur.
function add_favori($user_id, $soignant_id)
{
    $connection = open_connection();

    // Le soignant doit simplement exister dans l'application.
    $check_sql = "SELECT id
                  FROM soignants
                  WHERE id = ?";

    $check = mysqli_prepare($connection, $check_sql);

    mysqli_stmt_bind_param(
        $check,
        "i",
        $soignant_id
    );

    mysqli_stmt_execute($check);

    $check_result = mysqli_stmt_get_result($check);

    if (!mysqli_fetch_assoc($check_result)) {
        mysqli_stmt_close($check);
        close_connection($connection);
        return false;
    }

    mysqli_stmt_close($check);

    // Le couple utilisateur/soignant est unique en BDD.
    $sql = "INSERT IGNORE INTO favoris
            (id_user, id_soignant)
            VALUES (?, ?)";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "ii",
        $user_id,
        $soignant_id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// Supprime un soignant des favoris.
function remove_favori($user_id, $soignant_id)
{
    $connection = open_connection();

    $sql = "DELETE FROM favoris
            WHERE id_user = ?
            AND id_soignant = ?";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "ii",
        $user_id,
        $soignant_id
    );

    $result = mysqli_stmt_execute($request);

    mysqli_stmt_close($request);
    close_connection($connection);

    return $result;
}

// Retourne uniquement les soignants favoris de l'utilisateur.
function get_favoris_by_user($user_id)
{
    $connection = open_connection();

    $sql = "SELECT
                s.id,
                s.id_user,
                s.nom,
                s.prenom,
                s.date_naissance,
                s.adresse,
                s.mail,
                s.id_discipline,
                d.nom AS discipline,
                f.created_at AS favori_created_at
            FROM favoris f
            INNER JOIN soignants s
                ON f.id_soignant = s.id
            LEFT JOIN disciplines d
                ON s.id_discipline = d.id
            WHERE f.id_user = ?
            ORDER BY f.created_at DESC";

    $request = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $request,
        "i",
        $user_id
    );

    mysqli_stmt_execute($request);

    $result = mysqli_stmt_get_result($request);

    $favoris = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $favoris[] = $row;
    }

    mysqli_stmt_close($request);
    close_connection($connection);

    return $favoris;
}

// Miroir du NOM/prénom de son compte vers sa propre fiche uniquement.
function sync_own_soignant_identity($user_id, $nom, $prenom, $date_naissance)
{
    $db = open_connection();
    $q = mysqli_prepare($db, 'UPDATE soignants SET nom=?,prenom=?,date_naissance=? WHERE id_user=?');
    mysqli_stmt_bind_param($q, 'sssi', $nom, $prenom, $date_naissance, $user_id);
    $ok = mysqli_stmt_execute($q);
    mysqli_stmt_close($q);
    close_connection($db);
    return $ok;
}

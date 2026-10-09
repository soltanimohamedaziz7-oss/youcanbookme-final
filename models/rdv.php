<?php
// Les opérations de rendez-vous vérifient les droits et utilisent des transactions pour garder les créneaux cohérents.
// Rendez-vous et disponibilités : mysqli, transactions, contrôles côté serveur.
require_once __DIR__ . '/database.php';

function get_rdvs_by_user($user_id)
{
    $db = open_connection();
    $q = mysqli_prepare($db, "SELECT r.id,r.date,r.description,s.id AS id_soignant,s.nom,s.prenom,d.nom AS discipline
        FROM rdvs r JOIN soignants s ON s.id=r.id_soignant
        LEFT JOIN disciplines d ON d.id=s.id_discipline
        WHERE r.id_user=? ORDER BY r.date ASC");
    mysqli_stmt_bind_param($q, 'i', $user_id);
    mysqli_stmt_execute($q);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($q), MYSQLI_ASSOC);
    mysqli_stmt_close($q);
    close_connection($db);
    return $rows;
}

function get_rdvs_by_soignant($soignant_id)
{
    $db = open_connection();
    $q = mysqli_prepare($db, "SELECT r.id,r.date,r.description,r.id_user,
        s.id AS id_soignant,s.nom,s.prenom,d.nom AS discipline,
        u.nom AS patient_nom,u.prenom AS patient_prenom,u.mail AS patient_mail
        FROM rdvs r JOIN soignants s ON s.id=r.id_soignant
        JOIN users u ON u.id=r.id_user
        LEFT JOIN disciplines d ON d.id=s.id_discipline
        WHERE r.id_soignant=? ORDER BY r.date ASC");
    mysqli_stmt_bind_param($q, 'i', $soignant_id);
    mysqli_stmt_execute($q);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($q), MYSQLI_ASSOC);
    mysqli_stmt_close($q);
    close_connection($db);
    return $rows;
}

function get_owned_soignant_id($user_id)
{
    $db = open_connection();
    $q = mysqli_prepare($db, "SELECT s.id FROM soignants s JOIN users u ON u.id=s.id_user AND u.role='soignant' WHERE s.id_user=? LIMIT 1");
    mysqli_stmt_bind_param($q, 'i', $user_id);
    mysqli_stmt_execute($q);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    mysqli_stmt_close($q);
    close_connection($db);
    return $row ? (int) $row['id'] : 0;
}

function find_patients($search)
{
    $db = open_connection();
    $pattern = '%' . trim($search) . '%';
    $q = mysqli_prepare($db, "SELECT id,nom,prenom,mail FROM users WHERE role='patient'
        AND (nom LIKE ? OR prenom LIKE ? OR mail LIKE ? OR CONCAT(prenom,' ',nom) LIKE ?)
        ORDER BY nom,prenom LIMIT 50");
    mysqli_stmt_bind_param($q, 'ssss', $pattern, $pattern, $pattern, $pattern);
    mysqli_stmt_execute($q);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($q), MYSQLI_ASSOC);
    mysqli_stmt_close($q);
    close_connection($db);
    return $rows;
}

function patient_exists($id)
{
    $db = open_connection();
    $q = mysqli_prepare($db, "SELECT id FROM users WHERE id=? AND role='patient'");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $exists = (bool) mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    mysqli_stmt_close($q);
    close_connection($db);
    return $exists;
}

// Les créneaux du calendrier de démonstration sont générés une seule fois par date.
// Les rendez-vous déjà enregistrés restent occupés.
function ensure_disponibilites($id_soignant)
{
    if ($id_soignant <= 0) return false;
    $db = open_connection();
    $q = mysqli_prepare($db, 'SELECT id FROM soignants WHERE id=?');
    mysqli_stmt_bind_param($q, 'i', $id_soignant);
    mysqli_stmt_execute($q);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    mysqli_stmt_close($q);
    if (!$exists) {
        close_connection($db);
        return false;
    }
    // Ne pas régénérer 120+ créneaux à chaque affichage de la page.
    $qCount = mysqli_prepare($db, "SELECT COUNT(*) AS total FROM disponibilites WHERE id_soignant=? AND date_heure>=NOW() AND date_heure<DATE_ADD(NOW(), INTERVAL 25 DAY)");
    mysqli_stmt_bind_param($qCount, 'i', $id_soignant);
    mysqli_stmt_execute($qCount);
    $countRow = mysqli_fetch_assoc(mysqli_stmt_get_result($qCount));
    mysqli_stmt_close($qCount);
    if ((int)$countRow['total'] >= 6) {
        close_connection($db);
        return true;
    }
    $q = mysqli_prepare($db, "INSERT IGNORE INTO disponibilites(id_soignant,date_heure,disponible)
        VALUES(?,?,IF(EXISTS(SELECT 1 FROM rdvs WHERE id_soignant=? AND date=?),0,1))");
    $day = new DateTimeImmutable('today');
    foreach (range(0, 29) as $offset) {
        $today = $day->modify('+' . $offset . ' days');
        if ((int)$today->format('N') > 5) continue;
        foreach (['09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00'] as $hour) {
            $date = $today->format('Y-m-d') . ' ' . $hour;
            mysqli_stmt_bind_param($q, 'isis', $id_soignant, $date, $id_soignant, $date);
            mysqli_stmt_execute($q);
        }
    }
    mysqli_stmt_close($q);
    close_connection($db);
    return true;
}

// Inclure aussi les horaires occupés : le front les affiche en gris.
function get_disponibilites($id_soignant, $user_id = 0)
{
    $db = open_connection();
    $maintenant = date('Y-m-d H:i:s');
    $q = mysqli_prepare($db, "SELECT d.id,d.date_heure,
        IF(d.disponible=1 AND NOT EXISTS(SELECT 1 FROM rdvs r WHERE r.id_soignant=d.id_soignant AND r.date=d.date_heure),1,0) AS disponible
        FROM disponibilites d WHERE d.id_soignant=? AND d.date_heure>=?
        ORDER BY d.date_heure ASC LIMIT 180");
    mysqli_stmt_bind_param($q, 'is', $id_soignant, $maintenant);
    mysqli_stmt_execute($q);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($q), MYSQLI_ASSOC);
    mysqli_stmt_close($q);
    close_connection($db);
    foreach ($rows as &$row) {
        $row['disponible'] = (int) $row['disponible'];
        $row['date_heure_input'] = date('Y-m-d\TH:i', strtotime($row['date_heure']));
        $row['date_heure_affichage'] = date('d/m/Y à H:i', strtotime($row['date_heure']));
    }
    unset($row);
    return $rows;
}

function normalized_rdv_date($date)
{
    $date = str_replace('T', ' ', trim((string)$date));
    $object = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $date);
    if (!$object || $object->format('Y-m-d H:i') !== $date || $object <= new DateTimeImmutable()) return null;
    return $object->format('Y-m-d H:i:s');
}

function create_rdv($id_soignant, $id_user, $date, $description)
{
    $date = normalized_rdv_date($date);
    if (!$date || $id_soignant <= 0 || !patient_exists($id_user)) return false;
    $db = open_connection();
    mysqli_begin_transaction($db);
    try {
        $q = mysqli_prepare($db, "UPDATE disponibilites SET disponible=0 WHERE id_soignant=? AND date_heure=? AND disponible=1
            AND NOT EXISTS(SELECT 1 FROM rdvs WHERE id_soignant=? AND date=?)");
        mysqli_stmt_bind_param($q, 'isis', $id_soignant, $date, $id_soignant, $date);
        mysqli_stmt_execute($q);
        $claimed = mysqli_stmt_affected_rows($q) === 1;
        mysqli_stmt_close($q);
        if (!$claimed) throw new RuntimeException('Créneau pris');
        $q = mysqli_prepare($db, 'INSERT INTO rdvs (id_soignant,id_user,date,description) VALUES (?,?,?,?)');
        mysqli_stmt_bind_param($q, 'iiss', $id_soignant, $id_user, $date, $description);
        mysqli_stmt_execute($q);
        mysqli_stmt_close($q);
        mysqli_commit($db);
        return true;
    } catch (Throwable $e) {
        mysqli_rollback($db);
        return false;
    } finally {
        close_connection($db);
    }
}

// Un patient gère SES rendez-vous, un soignant ceux de SON agenda uniquement.
function find_authorized_rdv($id, $user_id, $role)
{
    $db = open_connection();
    if ($role === 'soignant') {
        $sql = 'SELECT r.* FROM rdvs r JOIN soignants s ON s.id=r.id_soignant WHERE r.id=? AND s.id_user=?';
    } else {
        $sql = 'SELECT r.* FROM rdvs r WHERE r.id=? AND r.id_user=?';
    }
    $q = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($q, 'ii', $id, $user_id);
    mysqli_stmt_execute($q);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    mysqli_stmt_close($q);
    close_connection($db);
    return $row;
}

// Annulation par le patient concerné OU le propriétaire du profil soignant.
// Suppression et libération du créneau sont effectuées dans une transaction.
function delete_rdv($id, $user_id, $role = 'patient')
{
    if ($id <= 0 || $user_id <= 0 || !in_array($role, ['patient', 'soignant'], true)) return false;
    $db = open_connection();
    try {
        mysqli_begin_transaction($db);
        $sql = $role === 'soignant'
            ? 'SELECT r.id,r.id_soignant,r.date FROM rdvs r JOIN soignants s ON s.id=r.id_soignant WHERE r.id=? AND s.id_user=? FOR UPDATE'
            : 'SELECT id,id_soignant,date FROM rdvs WHERE id=? AND id_user=? FOR UPDATE';
        $q = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($q, 'ii', $id, $user_id);
        mysqli_stmt_execute($q);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
        mysqli_stmt_close($q);
        if (!$old) throw new RuntimeException('Rendez-vous non autorisé ou introuvable');

        $q = mysqli_prepare($db, 'DELETE FROM rdvs WHERE id=?');
        mysqli_stmt_bind_param($q, 'i', $id);
        mysqli_stmt_execute($q);
        $deleted = mysqli_stmt_affected_rows($q) === 1;
        mysqli_stmt_close($q);
        if (!$deleted) throw new RuntimeException('Suppression impossible');

        // Ne jamais rouvrir un créneau si une réservation y existe encore.
        $q = mysqli_prepare($db, 'UPDATE disponibilites SET disponible=1 WHERE id_soignant=? AND date_heure=? AND NOT EXISTS (SELECT 1 FROM rdvs WHERE id_soignant=? AND date=?)');
        $sid = (int)$old['id_soignant'];
        $date = $old['date'];
        mysqli_stmt_bind_param($q, 'isis', $sid, $date, $sid, $date);
        mysqli_stmt_execute($q);
        mysqli_stmt_close($q);
        mysqli_commit($db);
        return true;
    } catch (Throwable $e) {
        mysqli_rollback($db);
        error_log('Annulation RDV échouée : ' . $e->getMessage());
        return false;
    } finally {
        close_connection($db);
    }
}

// Même transaction pour les deux rôles ; vérification de propriété EN BDD.
// Le soignant ne peut toucher qu'aux RDV de son agenda et le patient qu'aux siens.
function update_rdv_by_role($rdv_id, $account_id, $role, $date, $description)
{
    if (!in_array($role, ['patient', 'soignant'], true)) return false;
    $date = normalized_rdv_date($date);
    if (!$date || $rdv_id <= 0 || $account_id <= 0) return false;

    $db = open_connection();
    mysqli_begin_transaction($db);
    try {
        if ($role === 'soignant') {
            $sql = 'SELECT r.id,r.date,r.id_soignant FROM rdvs r JOIN soignants s ON s.id=r.id_soignant WHERE r.id=? AND s.id_user=? FOR UPDATE';
        } else {
            $sql = 'SELECT id,date,id_soignant FROM rdvs WHERE id=? AND id_user=? FOR UPDATE';
        }
        $q = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($q, 'ii', $rdv_id, $account_id);
        mysqli_stmt_execute($q);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
        mysqli_stmt_close($q);
        if (!$old) throw new RuntimeException('Accès refusé');

        $sid = (int)$old['id_soignant'];
        if ($date !== $old['date']) {
            // Réserver atomiquement le nouvel horaire avant de libérer l'ancien.
            $q = mysqli_prepare($db, "UPDATE disponibilites SET disponible=0 WHERE id_soignant=? AND date_heure=? AND disponible=1
                AND NOT EXISTS (SELECT 1 FROM rdvs WHERE id_soignant=? AND date=?)");
            mysqli_stmt_bind_param($q, 'isis', $sid, $date, $sid, $date);
            mysqli_stmt_execute($q);
            $claimed = mysqli_stmt_affected_rows($q) === 1;
            mysqli_stmt_close($q);
            if (!$claimed) throw new RuntimeException('Créneau indisponible');
        }

        $q = mysqli_prepare($db, 'UPDATE rdvs SET date=?,description=? WHERE id=?');
        mysqli_stmt_bind_param($q, 'ssi', $date, $description, $rdv_id);
        mysqli_stmt_execute($q);
        mysqli_stmt_close($q);

        if ($date !== $old['date']) {
            $q = mysqli_prepare($db, 'UPDATE disponibilites SET disponible=1 WHERE id_soignant=? AND date_heure=? AND NOT EXISTS (SELECT 1 FROM rdvs WHERE id_soignant=? AND date=?)');
            mysqli_stmt_bind_param($q, 'isis', $sid, $old['date'], $sid, $old['date']);
            mysqli_stmt_execute($q);
            mysqli_stmt_close($q);
        }
        mysqli_commit($db);
        return true;
    } catch (Throwable $e) {
        mysqli_rollback($db);
        return false;
    } finally {
        close_connection($db);
    }
}

// Compatibilité éventuelle avec les anciens appels côté soignant.
function update_rdv_by_soignant($rdv_id, $account_id, $date, $description)
{
    return update_rdv_by_role($rdv_id, $account_id, 'soignant', $date, $description);
}

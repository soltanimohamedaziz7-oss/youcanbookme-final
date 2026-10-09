<?php
// ============================================================
// CONFIGURATION GENERALE
// ============================================================

// Les identifiants réels sont conservés dans un fichier local non versionné.
// Copiez config-sample.php vers config-private.php puis adaptez les valeurs.
$private_config = __DIR__ . '/config-private.php';
if (!is_file($private_config)) {
    http_response_code(503);
    exit('Configuration absente : copiez config-sample.php vers config-private.php.');
}
require_once $private_config;

// Fuseau horaire des rendez-vous en France.
date_default_timezone_set("Europe/Paris");

// Nom du projet
define("PROJECT_NAME", "Mémo RDV Médicaux");

// En-têtes de sécurité : le navigateur ne doit pas deviner le type des fichiers.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: DENY');
}

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    ini_set("session.use_strict_mode", "1");
    session_set_cookie_params([
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);

    session_start();
}

// Création d'un token CSRF.
// Il sert à protéger les formulaires contre certaines attaques.
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Fonction utilisée dans les formulaires.
function csrf_token()
{
    return $_SESSION["csrf_token"];
}

// Vérifie le token reçu par un formulaire.
function verify_csrf()
{
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        die("Erreur de sécurité : formulaire invalide.");
    }
}

function current_role() { return $_SESSION['role'] ?? 'patient'; }
function is_soignant() { return current_role() === 'soignant'; }

<?php
// 
// PETITE FONCTION POUR PROTEGER L'AFFICHAGE HTML
// 

function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Calcul automatique de l'âge à partir de la date de naissance.
function calculer_age($date_naissance)
{
    if (empty($date_naissance)) {
        return "";
    }

    try {
        $naissance = new DateTime($date_naissance);
        $aujourd_hui = new DateTime();

        return $naissance->diff($aujourd_hui)->y;
    } catch (Exception $e) {
        return "";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title><?= e(PROJECT_NAME) ?></title>

    <link
        rel="stylesheet"
        href="views/css/style.css"
    >
</head>

<body>

<header class="header">
    <a class="logo" href="index.php?route=dashboard"><span class="brand-heart">♥</span><span>YouCanBookMe<small>Mon suivi médical</small></span></a>
    <?php if (!empty($_SESSION["user_id"])): ?>
    <div class="header-user"><span class="user-avatar">●</span> Bonjour, <?= e($_SESSION["user_prenom"] ?? "") ?> (<?= is_soignant() ? "Soignant" : "Patient" ?>)
      <button class="menu-button" type="button" onclick="toggleMenu()" aria-label="Ouvrir le menu" aria-controls="mobileMenu" aria-expanded="false">⌄</button>
    </div>
    <?php endif; ?>
</header>
<?php if (!empty($_SESSION["user_id"])): ?>
<nav id="mobileMenu" class="menu" aria-label="Navigation principale">
    <a href="index.php?route=dashboard">⌂ <span>Accueil</span></a>
    <a href="index.php?route=<?= is_soignant() ? 'mes-rdvs-soignant' : 'rdvs' ?>">▦ <span><?= is_soignant() ? 'Tous mes rendez-vous' : 'Mes rendez-vous' ?></span></a>
    <?php if (!is_soignant()): ?><a href="index.php?route=mes-soignants">♥ <span>Favoris</span></a><?php endif; ?>
    <?php if (!is_soignant()): ?><a href="index.php?route=soignants">♟ <span>Soignants</span></a><?php endif; ?>
    <a href="index.php?route=profil">◉ <span>Profil</span></a>
    <a class="logout-link" href="index.php?route=logout">↪ <span>Déconnexion</span></a>
</nav>
<?php endif; ?>
<main class="container">

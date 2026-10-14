<?php
// 
// PETITE FONCTION POUR PROTEGER L'AFFICHAGE HTML
// Cette fonction est réutilisée dans plusieurs pages du projet.
// 

// Je crée une fonction appelée e().
// Elle permet de sécuriser l'affichage des valeurs dans le HTML.
function e($value)
{
    // htmlspecialchars() transforme certains caractères spéciaux en entités HTML.
    // Cela évite notamment qu'un texte saisi par un utilisateur soit interprété comme du code HTML.
    //
    // (string) transforme la valeur en texte.
    // ?? "" utilise une chaîne vide si la valeur est nulle.
    //
    // ENT_QUOTES permet aussi de traiter les guillemets.
    // UTF-8 indique l'encodage utilisé pour les caractères.
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Calcul automatique de l'âge à partir de la date de naissance.
// Cette fonction peut être utilisée dans différentes pages du projet.
function calculer_age($date_naissance)
{
    // Si aucune date de naissance n'est disponible,
    // je retourne une chaîne vide.
    if (empty($date_naissance)) {
        return "";
    }

    // J'utilise try pour essayer d'effectuer le calcul.
    // Cela permet de gérer une éventuelle erreur liée à la date.
    try {

        // Je crée un objet DateTime à partir de la date de naissance.
        $naissance = new DateTime($date_naissance);

        // Je récupère la date du jour.
        $aujourd_hui = new DateTime();

        // Je calcule la différence entre les deux dates.
        // diff() calcule l'écart et y récupère le nombre d'années complètes.
        return $naissance->diff($aujourd_hui)->y;

    } catch (Exception $e) {

        // Si une erreur se produit pendant le calcul,
        // je retourne une chaîne vide au lieu d'afficher une erreur.
        return "";
    }
}
?>

<!--
    Je commence la structure HTML de la page.
    Le fichier layout.php contient les éléments communs
    aux différentes pages de mon application.
-->
<!DOCTYPE html>

<!-- J'indique que la langue principale du site est le français. -->
<html lang="fr">

<head>

    <!-- J'utilise UTF-8 pour afficher correctement les accents et les caractères spéciaux. -->
    <meta charset="UTF-8">

    <!--
        Cette balise permet d'adapter la largeur de la page
        à la taille de l'écran, notamment sur téléphone.
        Cela participe au fonctionnement responsive du site.
    -->
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!--
        J'affiche le nom du projet dans l'onglet du navigateur.
        PROJECT_NAME est une constante définie dans le projet.
        e() sécurise la valeur avant son affichage.
    -->
    <title><?= e(PROJECT_NAME) ?></title>

    <!--
        Je relie le fichier CSS à la page.
        Il contient les règles qui définissent l'apparence du site :
        couleurs, tailles, espacements et mise en page.
    -->
    <link
        rel="stylesheet"
        href="views/css/style.css"
    >

</head>

<body>

<!--
    HEADER : EN-TÊTE DU SITE
    Cette partie est commune aux pages qui utilisent ce fichier.
-->
<header class="header">

    <!--
        Le logo permet de revenir à la page d'accueil du compte connecté.
        Le lien passe par index.php, qui utilise la route dashboard.
    -->
    <a class="logo" href="index.php?route=dashboard"><span class="brand-heart">♥</span><span>YouCanBookMe<small>Mon suivi médical</small></span></a>

    <?php
    // Je vérifie si un utilisateur est connecté.
    // user_id est une information conservée dans la session après connexion.
    if (!empty($_SESSION["user_id"])):
    ?>

    <!--
        Si l'utilisateur est connecté, j'affiche son prénom
        et le type de compte utilisé.
    -->
    <div class="header-user">

        <!-- Petit symbole utilisé comme avatar. -->
        <span class="user-avatar">●</span>

        <!--
            J'affiche le prénom enregistré dans la session.
            Si le prénom n'existe pas, j'affiche une chaîne vide.
            is_soignant() permet de distinguer le compte soignant du compte patient.
        -->
        Bonjour, <?= e($_SESSION["user_prenom"] ?? "") ?> (<?= is_soignant() ? "Soignant" : "Patient" ?>)

      <!--
          Ce bouton permet d'ouvrir ou de fermer le menu.
          onclick appelle la fonction JavaScript toggleMenu().
          aria-label décrit le bouton pour l'accessibilité.
          aria-controls indique le menu concerné.
          aria-expanded indique si le menu est ouvert ou fermé.
      -->
      <button class="menu-button" type="button" onclick="toggleMenu()" aria-label="Ouvrir le menu" aria-controls="mobileMenu" aria-expanded="false">⌄</button>

    </div>

    <?php endif; ?>

</header>

<?php
// J'affiche le menu principal uniquement si un utilisateur est connecté.
if (!empty($_SESSION["user_id"])):
?>

<!--
    MENU DE NAVIGATION
    Il permet de passer d'une fonctionnalité à une autre.
    L'identifiant mobileMenu permet au JavaScript de retrouver ce menu.
-->
<nav id="mobileMenu" class="menu" aria-label="Navigation principale">

    <!-- Lien vers le tableau de bord. -->
    <a href="index.php?route=dashboard">⌂ <span>Accueil</span></a>

    <!--
        J'adapte le lien et son texte selon le type de compte.
        Un soignant accède à ses rendez-vous patients.
        Un patient accède à ses propres rendez-vous.
    -->
    <a href="index.php?route=<?= is_soignant() ? 'mes-rdvs-soignant' : 'rdvs' ?>">▦ <span><?= is_soignant() ? 'Tous mes rendez-vous' : 'Mes rendez-vous' ?></span></a>

    <!--
        Je propose le lien Favoris uniquement aux patients.
        Le soignant ne voit pas ce lien dans son menu.
    -->
    <?php if (!is_soignant()): ?><a href="index.php?route=mes-soignants">♥ <span>Favoris</span></a><?php endif; ?>

    <!--
        Je propose la liste des soignants uniquement aux patients.
    -->
    <?php if (!is_soignant()): ?><a href="index.php?route=soignants">♟ <span>Soignants</span></a><?php endif; ?>

    <!-- Lien vers la page de gestion du profil personnel. -->
    <a href="index.php?route=profil">◉ <span>Profil</span></a>

    <!--
        Lien permettant de demander la déconnexion.
        La route logout est traitée par le code PHP du projet.
    -->
    <a class="logout-link" href="index.php?route=logout">↪ <span>Déconnexion</span></a>

</nav>

<?php endif; ?>

<!--
    Je commence la zone principale du contenu de la page.
    La classe container permet d'appliquer une mise en page commune grâce au CSS.
    Les pages qui utilisent layout.php affichent leur contenu dans cette zone.
-->
<main class="container">
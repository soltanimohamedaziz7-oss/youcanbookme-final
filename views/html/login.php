<?php
// J'inclus la mise en page générale de mon site.
require __DIR__ . "/../layout.php"; ?>

<!-- Partie 1 : Bloc qui contient le formulaire de connexion. -->
<section class="auth-card">

    <!-- Titre de la page. -->
    <h1>
        Connexion
    </h1>

    <!-- Petit texte qui présente la fonction de la page. -->
    <p>
        Connectez-vous à votre mémo de rendez-vous médicaux.
    </p>

    <!-- J'affiche un message si une erreur de connexion est présente. -->
    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Partie 2 : Formulaire qui envoie les informations au serveur. -->
    <form
        method="POST"
        action="index.php?route=login"
    >

        <!-- J'ajoute un jeton de sécurité au formulaire. -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <!-- Champ pour saisir l'adresse e-mail. -->
        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email"
            required
        >

        <!-- Champ pour saisir le mot de passe. -->
        <label for="password">
            Mot de passe
        </label>

        <input
            id="password"
            name="password"
            type="password"
            required
        >

        <!-- Ce bouton envoie les informations pour se connecter. -->
        <button
            class="button"
            type="submit"
        >
            Se connecter
        </button>

    </form>

    <!-- Partie 3 : Lien pour les personnes qui n'ont pas encore de compte. -->
    <p class="center">
        Pas encore de compte ?
        <a href="index.php?route=register">
            Créer un compte
        </a>
    </p>

</section>

<!-- Je ferme le contenu principal de la page. -->
</main>

<!-- Je charge le JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
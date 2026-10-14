<?php
// J'inclus la mise en page générale de mon site.
require __DIR__ . "/../layout.php"; ?>

<!-- Partie 1 : Titre et présentation de la page du profil. -->
<section class="page-header">

    <h1>
        Mon profil
    </h1>

    <p>
        Mes informations personnelles.
    </p>

</section>

<!-- J'affiche un message si la modification du profil a réussi. -->
<?php if (!empty($success)): ?>
    <div class="alert success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<!-- J'affiche un message si une erreur est présente. -->
<?php if (!empty($error)): ?>
    <div class="alert error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<!-- Partie 2 : Formulaire contenant les informations personnelles de l'utilisateur. -->
<section class="card">

    <!-- J'envoie les informations du formulaire au serveur avec la méthode POST. -->
    <form
        method="POST"
        action="index.php?route=profil"
    >

        <!-- J'ajoute un jeton de sécurité au formulaire. -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <!-- J'affiche le type de compte sans permettre sa modification. -->
        <label for="type_compte">Type de compte</label>
        <input id="type_compte" type="text" value="<?= e(($user["role"] ?? "patient") === "soignant" ? "Soignant" : "Patient") ?>" disabled>

        <!-- Champ pour afficher et modifier le nom. -->
        <label for="nom">
            Nom
        </label>

        <input
            id="nom"
            name="nom"
            type="text"
            value="<?= e($user["nom"] ?? "") ?>"
            required
        >

        <!-- Champ pour afficher et modifier le prénom. -->
        <label for="prenom">
            Prénom
        </label>

        <input
            id="prenom"
            name="prenom"
            type="text"
            value="<?= e($user["prenom"] ?? "") ?>"
            required
        >

        <!-- Champ pour afficher et modifier la date de naissance. -->
        <label for="date_naissance">
            Date de naissance
        </label>

        <input
            id="date_naissance"
            name="date_naissance"
            type="date"
            value="<?= e($user["date_naissance"] ?? "") ?>"
            required
        >

        <!-- Je calcule l'âge à partir de la date de naissance. -->
        <div class="info-box">
            <strong>
                Âge :
            </strong>

            <?= e(calculer_age($user["date_naissance"] ?? "")) ?>
            ans
        </div>

        <!-- J'affiche l'adresse e-mail sans permettre sa modification. -->
        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            type="email"
            value="<?= e($user["mail"] ?? "") ?>"
            disabled
        >

        <!-- J'explique pourquoi l'adresse e-mail est bloquée. -->
        <p class="small-text">
            L'email ne peut pas être modifié depuis cette page.
        </p>

        <!-- J'affiche la date d'inscription enregistrée pour le compte. -->
        <label>
            Date d'inscription
        </label>

        <input
            type="text"
            value="<?= e($user["created_at"] ?? "") ?>"
            disabled
        >

        <!-- J'indique que cette date est enregistrée automatiquement. -->
        <p class="small-text">
            La date d'inscription est enregistrée automatiquement.
        </p>

        <!-- Si l'utilisateur est un soignant, je lui donne accès à sa fiche professionnelle. -->
        <?php if (is_soignant()): ?>
        <p class="small-text">Votre spécialité et votre adresse professionnelle sont modifiables depuis votre fiche professionnelle.</p>
        <a class="button secondary" href="index.php?route=soignant-edit&id=<?= (int)get_owned_soignant_id((int)$_SESSION["user_id"]) ?>">Ma fiche soignant</a>
        <?php endif; ?>

        <!-- Ce bouton envoie les modifications du profil au serveur. -->
        <button
            class="button"
            type="submit"
        >
            Modifier mon profil
        </button>

    </form>

</section>

<!-- Fin du contenu principal de la page. -->
</main>

<!-- Je charge le JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
<?php require __DIR__ . "/../layout.php"; ?>

<section class="page-header">

    <h1>
        Mon profil
    </h1>

    <p>
        Mes informations personnelles.
    </p>

</section>

<?php if (!empty($success)): ?>
    <div class="alert success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<section class="card">

    <form
        method="POST"
        action="index.php?route=profil"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <label for="type_compte">Type de compte</label>
        <input id="type_compte" type="text" value="<?= e(($user["role"] ?? "patient") === "soignant" ? "Soignant" : "Patient") ?>" disabled>

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

        <div class="info-box">
            <strong>
                Âge :
            </strong>

            <?= e(calculer_age($user["date_naissance"] ?? "")) ?>
            ans
        </div>

        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            type="email"
            value="<?= e($user["mail"] ?? "") ?>"
            disabled
        >

        <p class="small-text">
            L'email ne peut pas être modifié depuis cette page.
        </p>

        <label>
            Date d'inscription
        </label>

        <input
            type="text"
            value="<?= e($user["created_at"] ?? "") ?>"
            disabled
        >

        <p class="small-text">
            La date d'inscription est enregistrée automatiquement.
        </p>

        <?php if (is_soignant()): ?>
        <p class="small-text">Votre spécialité et votre adresse professionnelle sont modifiables depuis votre fiche professionnelle.</p>
        <a class="button secondary" href="index.php?route=soignant-edit&id=<?= (int)get_owned_soignant_id((int)$_SESSION["user_id"]) ?>">Ma fiche soignant</a>
        <?php endif; ?>

        <button
            class="button"
            type="submit"
        >
            Modifier mon profil
        </button>

    </form>

</section>

</main>

<script src="views/menu.js"></script>

</body>
</html>

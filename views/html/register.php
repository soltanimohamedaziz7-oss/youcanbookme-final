<?php require __DIR__ . "/../layout.php"; ?>

<section class="auth-card">

    <h1>
        Créer un compte
    </h1>

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="index.php?route=register"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <label for="nom">
            Nom
        </label>

        <input
            id="nom"
            name="nom"
            type="text"
            required
        >

        <label for="prenom">
            Prénom
        </label>

        <input
            id="prenom"
            name="prenom"
            type="text"
            required
        >

        <label for="date_naissance">
            Date de naissance
        </label>

        <input
            id="date_naissance"
            name="date_naissance"
            type="date"
            required
        >

        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email"
            required
        >

        <label for="role">Type de compte</label>
        <select id="role" name="role" required><option value="patient">Patient</option><option value="soignant">Soignant</option></select>

        <div id="champs-soignant" style="display:none">
            <label for="id_discipline">Spécialité du soignant</label>
            <select id="id_discipline" name="id_discipline">
                <option value="">Choisir une spécialité</option>
                <?php foreach (($disciplines ?? []) as $discipline): ?>
                <option value="<?= (int)$discipline['id'] ?>"><?= e($discipline['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="adresse">Adresse du cabinet</label>
            <input id="adresse" name="adresse" type="text" placeholder="Adresse professionnelle">
        </div>

        <label for="password">
            Mot de passe
        </label>

        <input
            id="password"
            name="password"
            type="password"
            minlength="8"
            required
        >

        <button
            class="button"
            type="submit"
        >
            Créer mon compte
        </button>

    </form>

    <p class="center">
        Déjà inscrit ?
        <a href="index.php?route=login">
            Se connecter
        </a>
    </p>

</section>

</main>

<script src="views/menu.js"></script>

</body>
</html>

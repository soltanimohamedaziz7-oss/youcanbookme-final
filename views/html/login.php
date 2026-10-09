<?php require __DIR__ . "/../layout.php"; ?>

<section class="auth-card">

    <h1>
        Connexion
    </h1>

    <p>
        Connectez-vous à votre mémo de rendez-vous médicaux.
    </p>

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="index.php?route=login"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
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

        <label for="password">
            Mot de passe
        </label>

        <input
            id="password"
            name="password"
            type="password"
            required
        >

        <button
            class="button"
            type="submit"
        >
            Se connecter
        </button>

    </form>

    <p class="center">
        Pas encore de compte ?
        <a href="index.php?route=register">
            Créer un compte
        </a>
    </p>

</section>

</main>

<script src="views/menu.js"></script>

</body>
</html>

<?php require __DIR__ . "/../layout.php"; ?>

<section class="page-header">

    <h1>
        Ajouter un soignant
    </h1>

</section>

<section class="card">

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="index.php?route=soignant-add">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>">

        <label for="nom">
            Nom
        </label>

        <input
            id="nom"
            name="nom"
            type="text"
            required>

        <label for="prenom">
            Prénom
        </label>

        <input
            id="prenom"
            name="prenom"
            type="text"
            required>

        <label for="date_naissance">
            Date de naissance
        </label>

        <input
            id="date_naissance"
            name="date_naissance"
            type="date"
            required>

        <label for="adresse">
            Adresse
        </label>

        <input
            id="adresse"
            name="adresse"
            type="text">

        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email">

        <label for="id_discipline">
            Discipline
        </label>

        <select
            id="id_discipline"
            name="id_discipline">

            <option value="0">
                Choisir une discipline
            </option>

            <?php foreach ($disciplines as $discipline): ?>

                <option
                    value="<?= (int) $discipline["id"] ?>">
                    <?= e($discipline["nom"]) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button
            class="button"
            type="submit">
            Ajouter le soignant
        </button>

    </form>

</section>

</main>

<script src="views/menu.js"></script>

</body>

</html>
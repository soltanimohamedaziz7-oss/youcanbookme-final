<?php require __DIR__ . "/../layout.php"; ?>

<section class="page-header">

    <h1>
        Modifier le soignant
    </h1>

</section>

<section class="card">

    <form
        method="POST"
        action="index.php?route=soignant-edit&id=<?= (int) $soignant["id"] ?>"
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
            value="<?= e($soignant["nom"]) ?>"
            required
        >

        <label for="prenom">
            Prénom
        </label>

        <input
            id="prenom"
            name="prenom"
            type="text"
            value="<?= e($soignant["prenom"]) ?>"
            required
        >

        <label for="date_naissance">
            Date de naissance
        </label>

        <input
            id="date_naissance"
            name="date_naissance"
            type="date"
            value="<?= e($soignant["date_naissance"]) ?>"
            required
        >

        <label for="adresse">
            Adresse
        </label>

        <input
            id="adresse"
            name="adresse"
            type="text"
            value="<?= e($soignant["adresse"]) ?>"
        >

        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email"
            value="<?= e($soignant["mail"]) ?>"
        >

        <label for="id_discipline">
            Discipline
        </label>

        <select
            id="id_discipline"
            name="id_discipline"
        >

            <?php foreach ($disciplines as $discipline): ?>

                <option
                    value="<?= (int) $discipline["id"] ?>"
                    <?= (int) $soignant["id_discipline"] === (int) $discipline["id"] ? "selected" : "" ?>
                >
                    <?= e($discipline["nom"]) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button
            class="button"
            type="submit"
        >
            Enregistrer
        </button>

    </form>

</section>

</main>

<script src="views/menu.js"></script>

</body>
</html>

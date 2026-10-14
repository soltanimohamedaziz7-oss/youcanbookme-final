<?php
// J'inclus la mise en page générale de mon site.
require __DIR__ . "/../layout.php"; ?>

<!-- Partie 1 : Titre de la page. -->
<section class="page-header">

    <h1>
        Modifier le soignant
    </h1>

</section>

<!-- Partie 2 : Formulaire pour modifier les informations du soignant. -->
<section class="card">

    <!-- J'envoie les nouvelles informations au serveur avec la méthode POST. -->
    <form
        method="POST"
        action="index.php?route=soignant-edit&id=<?= (int) $soignant["id"] ?>"
    >

        <!-- J'ajoute un jeton de sécurité au formulaire. -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <!-- Champ pour modifier le nom du soignant. -->
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

        <!-- Champ pour modifier le prénom du soignant. -->
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

        <!-- Champ pour modifier la date de naissance. -->
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

        <!-- Champ pour modifier l'adresse du soignant. -->
        <label for="adresse">
            Adresse
        </label>

        <input
            id="adresse"
            name="adresse"
            type="text"
            value="<?= e($soignant["adresse"]) ?>"
        >

        <!-- Champ pour modifier l'adresse e-mail. -->
        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email"
            value="<?= e($soignant["mail"]) ?>"
        >

        <!-- Liste déroulante pour choisir la discipline du soignant. -->
        <label for="id_discipline">
            Discipline
        </label>

        <select
            id="id_discipline"
            name="id_discipline"
        >

            <!-- Je parcours les disciplines disponibles pour remplir la liste. -->
            <?php foreach ($disciplines as $discipline): ?>

                <option
                    value="<?= (int) $discipline["id"] ?>"
                    <?= (int) $soignant["id_discipline"] === (int) $discipline["id"] ? "selected" : "" ?>
                >
                    <?= e($discipline["nom"]) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <!-- Ce bouton envoie le formulaire pour enregistrer les modifications. -->
        <button
            class="button"
            type="submit"
        >
            Enregistrer
        </button>

    </form>

</section>

<!-- Partie 3 : Fin du contenu principal de la page. -->
</main>

<!-- Je charge le JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
<?php
// 
// PAGE LISTE DES SOIGNANTS
// 

require __DIR__ . "/../layout.php";
?>

<section class="page-header">

    <div>

        <h1>
            Mes soignants
        </h1>

        <p>
            Recherchez un soignant et ajoutez-le en favori.
        </p>

    </div>



</section>

<!-- 
     RECHERCHE
     je peut rechercher par nom, prénom ou discipline.
     Exemple : Dupont, Jean, Dentiste, Généraliste...
      -->

<form
    method="GET"
    action="index.php"
    class="search-form"
>

    <input
        type="hidden"
        name="route"
        value="soignants-search"
    >

    <input
        type="search"
        name="recherche"
        placeholder="Nom, prénom ou discipline..."
        value="<?= e($_GET["recherche"] ?? "") ?>"
    >

    <button
        class="button"
        type="submit"
    >
        Rechercher
    </button>

</form>

<div class="page-actions">

    <?php if (!is_soignant()): ?>
    <a
        class="button secondary"
        href="index.php?route=mes-soignants"
    >
        ❤️ Mes favoris
    </a>
    <?php endif; ?>

    <a
        class="button secondary"
        href="index.php?route=rdv-add"
    >
        📅 Prendre un RDV
    </a>

</div>

<?php if (empty($soignants)): ?>

    <section class="empty-card">

        <h2>
            Aucun soignant trouvé
        </h2>

        <?php if (!empty($_GET["recherche"])): ?>

            <p>
                Aucun résultat pour :
                <strong>
                    <?= e($_GET["recherche"]) ?>
                </strong>
            </p>

            <a
                class="button"
                href="index.php?route=soignants"
            >
                Afficher tous les soignants
            </a>

        <?php else: ?>

            <p>
                Vous n'avez pas encore ajouté de professionnel de santé.
            </p>

            <a class="button" href="index.php?route=soignants">Voir tous les soignants</a>

        <?php endif; ?>

    </section>

<?php else: ?>

    <section class="cards">

        <?php foreach ($soignants as $soignant): ?>

            <article class="card">

                <div class="soignant-card-header">

                    <div>

                        <h2>
                            <?= e($soignant["prenom"]) ?>
                            <?= e($soignant["nom"]) ?>
                        </h2>

                        <p>
                            <strong>
                                Discipline :
                            </strong>

                            <?= e($soignant["discipline"] ?? "Non renseignée") ?>
                        </p>

                    </div>

                    <!-- 
                         BOUTON FAVORI
                         Le coeur est vide si le soignant n'est pas favori.
                         Il devient rouge s'il est déjà enregistré.
                          -->

                    <?php if (!is_soignant()): ?>
                    <form
                        method="POST"
                        action="index.php?route=favori-toggle"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <input
                            type="hidden"
                            name="soignant_id"
                            value="<?= (int) $soignant["id"] ?>"
                        >

                        <?php if (!empty($soignant["favori_id"])): ?>

                            <button
                                class="favorite-button active"
                                type="submit"
                                title="Retirer des favoris"
                            >
                                ❤️
                            </button>

                        <?php else: ?>

                            <button
                                class="favorite-button"
                                type="submit"
                                title="Ajouter aux favoris"
                            >
                                ♡
                            </button>

                        <?php endif; ?>

                    </form>
                    <?php endif; ?>

                </div>

                <p>
                    <strong>
                        Âge :
                    </strong>

                    <?= e(calculer_age($soignant["date_naissance"])) ?>
                    ans
                </p>

                <div class="actions">

                    <a
                        class="button secondary"
                        href="index.php?route=soignant-detail&id=<?= (int) $soignant["id"] ?>"
                    >
                        Voir
                    </a>

                    <?php if (is_soignant() && (int)$soignant['id_user'] === (int)$_SESSION['user_id']): ?>
                    <a
                        class="button secondary"
                        href="index.php?route=soignant-edit&id=<?= (int) $soignant["id"] ?>"
                    >
                        Modifier
                    </a>
                    <?php endif; ?>

                    <a
                        class="button"
                        href="index.php?route=rdv-add&soignant_id=<?= (int) $soignant["id"] ?>"
                    >
                        Prendre un RDV
                    </a>

                    

                </div>

            </article>

        <?php endforeach; ?>

    </section>

<?php endif; ?>

</main>

<script src="views/menu.js"></script>

</body>
</html>

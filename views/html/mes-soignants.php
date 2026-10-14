<?php
// 
// PAGE MES SOIGNANTS FAVORIS
// 

// J'inclus la mise en page générale de mon site.
require __DIR__ . "/../layout.php";
?>

<!-- Partie 1 : Présentation de la page des soignants favoris. -->
<section class="page-header">

    <div>

        <h1>
            Mes soignants favoris
        </h1>

        <p>
            Retrouvez ici les professionnels que vous avez ajoutés avec le coeur.
        </p>

    </div>

</section>

<!-- Bouton pour accéder à la liste des soignants et en rechercher d'autres. -->
<div class="page-actions">

    <a
        class="button"
        href="index.php?route=soignants"
    >
        Rechercher un soignant
    </a>

</div>

<!-- Partie 2 : Je vérifie si l'utilisateur a des soignants favoris. -->
<?php if (empty($favoris)): ?>

    <!-- Si la liste est vide, j'affiche un message explicatif. -->
    <section class="empty-card">

        <h2>
            Aucun favori
        </h2>

        <p>
            Vous pouvez ajouter un soignant à cette page
            en cliquant sur le coeur.
        </p>

        <!-- Ce lien permet d'accéder à la liste des soignants. -->
        <a
            class="button"
            href="index.php?route=soignants"
        >
            Voir mes soignants
        </a>

    </section>

<!-- Si des favoris existent, j'affiche la liste des soignants. -->
<?php else: ?>

    <section class="cards">

        <!-- Je parcours chaque soignant enregistré dans la liste des favoris. -->
        <?php foreach ($favoris as $soignant): ?>

            <article class="card">

                <!-- En-tête de la carte avec l'identité et la discipline du soignant. -->
                <div class="soignant-card-header">

                    <div>

                        <!-- J'affiche le prénom et le nom du soignant. -->
                        <h2>
                            <?= e($soignant["prenom"]) ?>
                            <?= e($soignant["nom"]) ?>
                        </h2>

                        <!-- J'affiche sa discipline médicale. -->
                        <p>
                            <strong>
                                Discipline :
                            </strong>

                            <?= e($soignant["discipline"] ?? "Non renseignée") ?>
                        </p>

                    </div>

                    <!-- Partie 3 : Formulaire pour retirer un soignant des favoris. -->
                    <form
                        method="POST"
                        action="index.php?route=favori-toggle"
                    >

                        <!-- J'ajoute un jeton de sécurité au formulaire. -->
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <!-- Je transmets l'identifiant du soignant concerné. -->
                        <input
                            type="hidden"
                            name="soignant_id"
                            value="<?= (int) $soignant["id"] ?>"
                        >

                        <!-- Le bouton avec le coeur permet de retirer le favori. -->
                        <button
                            class="favorite-button active"
                            type="submit"
                            title="Retirer des favoris"
                        >
                            ❤️
                        </button>

                    </form>

                </div>

                <!-- J'affiche l'âge calculé à partir de la date de naissance. -->
                <p>
                    <strong>
                        Âge :
                    </strong>

                    <?= e(calculer_age($soignant["date_naissance"])) ?>
                    ans
                </p>

                <!-- J'affiche l'adresse uniquement si elle est renseignée. -->
                <?php if (!empty($soignant["adresse"])): ?>

                    <p>
                        <strong>
                            Adresse :
                        </strong>

                        <?= e($soignant["adresse"]) ?>
                    </p>

                <?php endif; ?>

                <!-- J'affiche l'e-mail uniquement s'il est renseigné. -->
                <?php if (!empty($soignant["mail"])): ?>

                    <p>
                        <strong>
                            Email :
                        </strong>

                        <?= e($soignant["mail"]) ?>
                    </p>

                <?php endif; ?>

                <!-- Partie 4 : Actions possibles pour chaque soignant favori. -->
                <div class="actions">

                    <!-- Lien pour consulter les informations détaillées du soignant. -->
                    <a
                        class="button secondary"
                        href="index.php?route=soignant-detail&id=<?= (int) $soignant["id"] ?>"
                    >
                        Voir
                    </a>

                    <!-- Lien pour prendre un rendez-vous avec ce soignant. -->
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

<!-- Fin du contenu principal de la page. -->
</main>

<!-- Je charge le JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>

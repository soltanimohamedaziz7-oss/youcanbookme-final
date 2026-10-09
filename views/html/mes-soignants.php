<?php
// 
// PAGE MES SOIGNANTS FAVORIS
// 

require __DIR__ . "/../layout.php";
?>

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

<div class="page-actions">

    <a
        class="button"
        href="index.php?route=soignants"
    >
        Rechercher un soignant
    </a>

</div>

<?php if (empty($favoris)): ?>

    <section class="empty-card">

        <h2>
            Aucun favori
        </h2>

        <p>
            Vous pouvez ajouter un soignant à cette page
            en cliquant sur le coeur.
        </p>

        <a
            class="button"
            href="index.php?route=soignants"
        >
            Voir mes soignants
        </a>

    </section>

<?php else: ?>

    <section class="cards">

        <?php foreach ($favoris as $soignant): ?>

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

                    <!-- Retirer le soignant des favoris. -->
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

                        <button
                            class="favorite-button active"
                            type="submit"
                            title="Retirer des favoris"
                        >
                            ❤️
                        </button>

                    </form>

                </div>

                <p>
                    <strong>
                        Âge :
                    </strong>

                    <?= e(calculer_age($soignant["date_naissance"])) ?>
                    ans
                </p>

                <?php if (!empty($soignant["adresse"])): ?>

                    <p>
                        <strong>
                            Adresse :
                        </strong>

                        <?= e($soignant["adresse"]) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($soignant["mail"])): ?>

                    <p>
                        <strong>
                            Email :
                        </strong>

                        <?= e($soignant["mail"]) ?>
                    </p>

                <?php endif; ?>

                <div class="actions">

                    <a
                        class="button secondary"
                        href="index.php?route=soignant-detail&id=<?= (int) $soignant["id"] ?>"
                    >
                        Voir
                    </a>

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

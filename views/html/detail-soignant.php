<?php require __DIR__ . "/../layout.php"; ?>

<section class="page-header">

    <h1>
        Détail du soignant
    </h1>

</section>

<section class="card">

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

    <p>
        <strong>
            Date de naissance :
        </strong>

        <?= e($soignant["date_naissance"]) ?>
    </p>

    <p>
        <strong>
            Âge :
        </strong>

        <?= e(calculer_age($soignant["date_naissance"])) ?>
        ans
    </p>

    <p>
        <strong>
            Adresse :
        </strong>

        <?= e($soignant["adresse"] ?? "") ?>
    </p>

    <p>
        <strong>
            Email :
        </strong>

        <?= e($soignant["mail"] ?? "") ?>
    </p>

    <div class="actions">
        <?php if (!is_soignant()): ?>
        <a class="button" href="index.php?route=rdv-add&soignant_id=<?= (int)$soignant['id'] ?>">Prendre un rendez-vous</a>
        <?php endif; ?>

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
            href="index.php?route=soignants"
        >
            Retour
        </a>

    </div>

</section>

</main>

<script src="views/menu.js"></script>

</body>
</html>

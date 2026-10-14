<?php
// J'inclus la mise en page générale de mon site.
require __DIR__ . "/../layout.php"; ?>

<!-- Partie 1 : Titre de la page. -->
<section class="page-header">

    <h1>Détail du soignant</h1>

</section>

<!-- Partie 2 : Affichage des informations du soignant sélectionné. -->
<section class="card">

    <!-- J'affiche le prénom et le nom du soignant. -->
    <h2>
        <?= e($soignant["prenom"]) ?>
        <?= e($soignant["nom"]) ?>
    </h2>

    <!-- J'affiche la discipline médicale du soignant. -->
    <p>
        <strong>
            Discipline :
        </strong>

        <?= e($soignant["discipline"] ?? "Non renseignée") ?>
    </p>

    <!-- J'affiche la date de naissance du soignant. -->
    <p>
        <strong>
            Date de naissance :
        </strong>

        <?= e($soignant["date_naissance"]) ?>
    </p>

    <!-- Je calcule et j'affiche l'âge à partir de la date de naissance. -->
    <p>
        <strong>
            Âge :
        </strong>

        <?= e(calculer_age($soignant["date_naissance"])) ?>
        ans
    </p>

    <!-- J'affiche l'adresse du soignant si elle est renseignée. -->
    <p>
        <strong>
            Adresse :
        </strong>

        <?= e($soignant["adresse"] ?? "") ?>
    </p>

    <!-- J'affiche l'adresse e-mail du soignant si elle est renseignée. -->
    <p>
        <strong>
            Email :
        </strong>

        <?= e($soignant["mail"] ?? "") ?>
    </p>

    <!-- Partie 3 : Boutons d'action disponibles selon le type d'utilisateur. -->
    <div class="actions">

        <!-- Un utilisateur qui n'est pas un soignant peut prendre un rendez-vous. -->
        <?php if (!is_soignant()): ?>
        <a class="button" href="index.php?route=rdv-add&soignant_id=<?= (int)$soignant['id'] ?>">Prendre un rendez-vous</a>
        <?php endif; ?>

        <!-- Le soignant peut modifier ses informations uniquement si cette fiche lui appartient. -->
        <?php if (is_soignant() && (int)$soignant['id_user'] === (int)$_SESSION['user_id']): ?>
        <a
            class="button secondary"
            href="index.php?route=soignant-edit&id=<?= (int) $soignant["id"] ?>"
        >
            Modifier
        </a>
        <?php endif; ?>

        <!-- Ce bouton permet de retourner à la liste des soignants. -->
        <a
            class="button"
            href="index.php?route=soignants"
        >
            Retour
        </a>

    </div>

</section>

<!-- Partie 4 : Fin du contenu principal de la page. -->
</main>

<!-- Je charge le JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
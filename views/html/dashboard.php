<?php require __DIR__ . "/../layout.php"; ?>

<section class="page-header">

    <h1>
        Bonjour <?= e($_SESSION["user_prenom"] ?? "") ?> 👋
    </h1>

    <p>
        Voici votre suivi médical du jour. Retrouvez toutes vos informations au même endroit.
    </p>

</section>

<section class="welcome-banner">
    <div><strong>Votre santé, notre priorité 💙</strong>
        <p>Gérez vos rendez-vous et vos professionnels de santé simplement.</p>
    </div><span aria-hidden="true">🗓️</span>
</section>
<section class="dashboard-grid">
    <?php if (!is_soignant()): ?>

        <a
            class="dashboard-card"
            href="index.php?route=soignants">
            <span class="icon">
                👨‍⚕️
            </span>

            <h2>
                Mes soignants
            </h2>

            <p>
                Voir et gérer mes professionnels de santé.
            </p>
        </a>

    <?php endif; ?>
    <a
        class="dashboard-card"
        href="index.php?route=<?= is_soignant() ? 'mes-rdvs-soignant' : 'rdvs' ?>">
        <span class="icon">
            📅
        </span>

        <h2>
            <?= is_soignant() ? 'Tous mes rendez-vous patients' : 'Mes rendez-vous' ?>
        </h2>

        <p>
            Consulter mes rendez-vous passés et à venir.
        </p>
    </a>

    <a
        class="dashboard-card"
        href="index.php?route=profil">
        <span class="icon">
            👤
        </span>

        <h2>
            Mon profil
        </h2>

        <p>
            Consulter et modifier mes informations personnelles.
        </p>
    </a>

    <a class="dashboard-card" href="index.php?route=rdv-add"><span class="icon">✚</span>
        <h2><?= is_soignant() ? 'Ajouter un rendez-vous patient' : 'Prendre un rendez-vous' ?></h2>
        <p><?= is_soignant() ? 'Rechercher un patient et réserver un horaire libre.' : 'Rechercher un médecin et choisir un créneau libre.' ?></p>
    </a>
</section>

</main>

<script src="views/menu.js"></script>

</body>

</html>
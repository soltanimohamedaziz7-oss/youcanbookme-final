<?php require __DIR__ . '/../layout.php'; ?>

<section class="page-header">
    <h1><?= $soignant_choisi ? 'Réserver un rendez-vous' : 'Prendre un rendez-vous' ?></h1>
    <p><?= $is_provider ? 'Recherchez un patient et réservez un créneau dans votre agenda.' : ($soignant_choisi ? 'Sélectionnez un horaire libre pour confirmer votre rendez-vous.' : 'Choisissez un soignant pour accéder à ses disponibilités.') ?></p>
</section>

<?php if (!empty($error)): ?>
    <div class="alert error" role="alert"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($soignant_choisi): ?>
    <?php if (!$is_provider): ?>
        <p><a class="button secondary" href="index.php?route=rdv-add">← Choisir un autre soignant</a></p>
    <?php endif; ?>

    <section class="card" aria-label="Soignant sélectionné" style="margin:16px 0">
        <h2><?= e($soignant_choisi['prenom'] . ' ' . $soignant_choisi['nom']) ?></h2>
        <p><strong>Discipline :</strong> <?= e($soignant_choisi['discipline'] ?? 'Non renseignée') ?></p>
    </section>

    <?php if ($is_provider): ?>
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="route" value="rdv-add">
            <input type="search" name="patient" placeholder="Rechercher un patient (nom, prénom, email)" value="<?= e($recherche_patient) ?>">
            <button type="submit" class="button">Rechercher un patient</button>
        </form>
    <?php endif; ?>

    <section class="card rdv-form-card" id="reservation" data-soignant-id="<?= (int)$soignant_selectionne ?>">
        <h2>Informations du rendez-vous</h2>
        <form method="POST" action="index.php?route=rdv-add&amp;soignant_id=<?= (int)$soignant_selectionne ?><?= $is_provider ? '&amp;patient=' . urlencode($recherche_patient) : '' ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id_soignant" value="<?= (int)$soignant_selectionne ?>">

            <?php if ($is_provider): ?>
                <label for="id_user">Patient</label>
                <select id="id_user" name="id_user" required>
                    <option value="">Choisir un patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= (int)$patient['id'] ?>" <?= (int)($_POST['id_user'] ?? 0) === (int)$patient['id'] ? 'selected' : '' ?>><?= e($patient['prenom'].' '.$patient['nom'].' ('.$patient['mail'].')') ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$patients): ?><p class="small-text">Recherchez d’abord un patient pour réserver un rendez-vous.</p><?php endif; ?>
            <?php endif; ?>

            <label for="date">Date et heure</label>
            <p>Les créneaux disponibles sont verts. Cliquez sur un créneau : il devient turquoise, comme la barre de navigation. Les créneaux gris sont déjà réservés.</p>
            <div class="actions creneaux-list" id="creneaux-serveur" role="group" aria-label="Choix du créneau">
                <?php foreach ($creneaux_page as $creneau): ?>
                    <?php $libre = (int)$creneau['disponible'] === 1; ?>
                    <button type="button" class="button creneau-btn<?= $libre ? '' : ' is-unavailable' ?>"
                        <?= $libre ? 'aria-pressed="false"' : 'disabled' ?>
                        data-creneau="<?= e($creneau['date_heure_input']) ?>"
                        data-label="<?= e($creneau['date_heure_affichage']) ?>">
                        <?= e($creneau['date_heure_affichage']) ?><?= $libre ? '' : ' — Déjà pris' ?>
                    </button>
                <?php endforeach; ?>
                <?php if (!$creneaux_page): ?><p>Aucun créneau affichable pour ce soignant pour le moment.</p><?php endif; ?>
            </div>
            <p class="creneau-selection-message" id="creneau-selection" role="status" aria-live="polite">Aucun créneau sélectionné.</p>
            <input id="date" name="date" type="datetime-local" min="<?= date('Y-m-d\TH:i') ?>" value="<?= e($_POST['date'] ?? '') ?>" required readonly>
            <p class="small-text">La date est remplie automatiquement lorsque vous choisissez un créneau turquoise ci-dessus.</p>

            <label for="description">Motif du rendez-vous (facultatif)</label>
            <textarea id="description" name="description" rows="4" placeholder="Exemple : consultation de contrôle..."><?= e($_POST['description'] ?? '') ?></textarea>
            <button class="button" type="submit" <?= !$creneaux_page ? 'disabled' : '' ?>>Confirmer le rendez-vous</button>
        </form>
    </section>
    <script>
        (function () {
            const liste = document.getElementById('creneaux-serveur');
            const champ = document.getElementById('date');
            const message = document.getElementById('creneau-selection');
            if (!liste || !champ || !message) return;

            function selectionner(bouton) {
                if (!bouton || bouton.disabled) return;
                liste.querySelectorAll('button[data-creneau]').forEach(function (autre) {
                    autre.classList.remove('is-selected');
                    if (!autre.disabled) autre.setAttribute('aria-pressed', 'false');
                });
                bouton.classList.add('is-selected');
                bouton.setAttribute('aria-pressed', 'true');
                champ.value = bouton.dataset.creneau;
                champ.dispatchEvent(new Event('change', { bubbles: true }));
                message.textContent = '✓ Créneau sélectionné : ' + bouton.dataset.label;
                message.classList.add('has-selection');
            }

            liste.addEventListener('click', function (event) {
                const bouton = event.target.closest('button[data-creneau]');
                if (bouton && liste.contains(bouton)) selectionner(bouton);
            });


            

            // Conserver l'effet après une erreur de validation du formulaire.
            if (champ.value) {
                const dejaChoisi = Array.from(liste.querySelectorAll('button[data-creneau]'))
                    .find(function (bouton) { return !bouton.disabled && bouton.dataset.creneau === champ.value; });
                if (dejaChoisi) selectionner(dejaChoisi);
            }
        })();
    </script>

<?php else: ?>
    <form method="GET" action="index.php" class="search-form">
        <input type="hidden" name="route" value="rdv-add">
        <input type="search" name="recherche" placeholder="Nom, prénom ou discipline..." value="<?= e($recherche) ?>">
        <button type="submit" class="button">Rechercher</button>
    </form>
    <?php if ($soignants): ?>
        <section class="search-results">
            <h2>Choisir un soignant</h2>
            <div class="cards">
                <?php foreach ($soignants as $soignant): ?>
                    <article class="card">
                        <h3><?= e($soignant['prenom'] . ' ' . $soignant['nom']) ?></h3>
                        <p><strong>Discipline :</strong> <?= e($soignant['discipline'] ?? 'Non renseignée') ?></p>
                        <a class="button" href="index.php?route=rdv-add&amp;soignant_id=<?= (int)$soignant['id'] ?>">Choisir ce soignant</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <section class="empty-card"><p>Aucun soignant trouvé.</p></section>
    <?php endif; ?>
<?php endif; ?>

</main>
<script src="views/menu.js"></script>
</body>
</html>

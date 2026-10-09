<?php require __DIR__ . "/../layout.php"; ?>

<?php if (!empty($notification)): ?>
    <div role="status" style="padding:14px 18px;margin:14px 0;border-radius:8px;background:<?= $notification['type'] === 'success' ? '#e6f4ea' : '#fde8e8' ?>;color:#222;">
        <?= e($notification['message']) ?>
    </div>
<?php endif; ?>

<section class="page-header">

    <div>
        <h1>
            <?= is_soignant() ? "Tous mes rendez-vous patients" : "Mes rendez-vous" ?>
        </h1>

        <p>
            <?= is_soignant() ? "Tous les rendez-vous de mon agenda, passés et à venir." : "Mon historique et mes prochains rendez-vous." ?>
        </p>
    </div>

    <a
        class="button"
        href="index.php?route=rdv-add">
        + Ajouter
    </a>

</section>

<?php if (empty($rdvs)): ?>

    <section class="empty-card">

        <h2>
            Aucun rendez-vous
        </h2>

        <p>
            Aucun rendez-vous n'est enregistré.
        </p>

    </section>

<?php else: ?>

    <section class="cards">

        <?php foreach ($rdvs as $rdv): ?>

            <article class="card">

                <h2>
                    <?= e($rdv["prenom"]) ?>
                    <?= e($rdv["nom"]) ?>
                </h2>

                <?php if (is_soignant()): ?><p><strong>Patient :</strong> <?= e(($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '')) ?> (<?= e($rdv['patient_mail'] ?? '') ?>)</p>
                <?php endif; ?>
                <p>
                    <strong>
                        Discipline :
                    </strong>

                    <?= e($rdv["discipline"] ?? "") ?>
                </p>

                <p>
                    <strong>
                        Date :
                    </strong>

                    <?= e($rdv["date"]) ?>
                </p>

                <p>
                    <strong>
                        Description :
                    </strong>

                    <?= e($rdv["description"] ?? "") ?>
                </p>

                <a class="button" href="index.php?route=rdv-edit&id=<?= (int)$rdv['id'] ?>">Modifier</a>

                <form
                    method="POST"
                    action="index.php?route=rdv-delete"
                    onsubmit="return confirmerAnnulationRdv(this);">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $rdv["id"] ?>">
                    <input type="hidden" name="rdv_label" value="<?= e(date('d/m/Y à H:i', strtotime($rdv['date']))) ?>">

                    <button
                        class="button danger"
                        type="submit"
                        aria-label="Annuler le rendez-vous du <?= e(date('d/m/Y à H:i', strtotime($rdv['date']))) ?>">
                        Annuler ce rendez-vous
                    </button>

                </form>

            </article>

        <?php endforeach; ?>

    </section>

<?php endif; ?>

</main>

<script>
    function confirmerAnnulationRdv(formulaire) {
        const date = formulaire.elements['rdv_label'].value;
        const ok = window.confirm('Annuler uniquement le rendez-vous du ' + date + ' ?\nLe créneau sera de nouveau disponible.');
        if (!ok) return false;
        const bouton = formulaire.querySelector('button[type="submit"]');
        if (bouton) {
            bouton.disabled = true;
            bouton.textContent = 'Annulation en cours…';
        }
        return true;
    }
</script>
<script src="views/menu.js"></script>

</body>

</html>
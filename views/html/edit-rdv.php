<?php 
require __DIR__ . '/../layout.php'; ?>

<section class="page-header"><h1>Modifier un rendez-vous</h1></section>

<?php 
if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="card rdv-form-card" data-soignant-id="<?= (int)$soignant_selectionne ?>" data-rdv-id="<?= (int)$rdv['id'] ?>">
    <form method="POST" action="index.php?route=rdv-edit&id=<?= (int)$rdv['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <p>Choisissez un nouvel horaire libre ci-dessus ou conservez votre horaire actuel.</p>
        <label for="date">Date et heure</label>
        <input id="date" type="datetime-local" name="date" value="<?= e(str_replace(' ', 'T', substr($rdv['date'], 0, 16))) ?>" required>
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= e($rdv['description']) ?></textarea>
        <button class="button" type="submit">Enregistrer les modifications</button>
        <a class="button secondary" href="index.php?route=rdvs">Retour à mes rendez-vous</a>
    </form>
</section>
</main><script src="views/menu.js"></script></body></html>

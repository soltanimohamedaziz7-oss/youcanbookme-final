<?php 
// J'inclus la mise en page générale de mon site.
require __DIR__ . '/../layout.php'; ?>

<!-- Partie 1 : Titre de la page. -->
<section class="page-header"><h1>Modifier un rendez-vous</h1></section>

<?php 
// J'affiche un message si une erreur est présente.
if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

<!-- Partie 2 : Formulaire pour modifier un rendez-vous existant. -->
<!-- Je transmets l'identifiant du soignant et celui du rendez-vous dans les attributs de cette section. -->
<section class="card rdv-form-card" data-soignant-id="<?= (int)$soignant_selectionne ?>" data-rdv-id="<?= (int)$rdv['id'] ?>">

    <!-- J'envoie les modifications au serveur avec la méthode POST. -->
    <form method="POST" action="index.php?route=rdv-edit&id=<?= (int)$rdv['id'] ?>">

        <!-- J'ajoute un jeton de sécurité au formulaire. -->
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <!-- J'informe l'utilisateur qu'il peut modifier l'horaire du rendez-vous. -->
        <p>Choisissez un nouvel horaire libre ci-dessus ou conservez votre horaire actuel.</p>

        <!-- Champ permettant de modifier la date et l'heure du rendez-vous. -->
        <label for="date">Date et heure</label>
        <input id="date" type="datetime-local" name="date" value="<?= e(str_replace(' ', 'T', substr($rdv['date'], 0, 16))) ?>" required>

        <!-- Champ permettant de modifier la description du rendez-vous. -->
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= e($rdv['description']) ?></textarea>

        <!-- Ce bouton envoie les nouvelles informations au serveur. -->
        <button class="button" type="submit">Enregistrer les modifications</button>

        <!-- Ce lien permet de revenir à la liste des rendez-vous. -->
        <a class="button secondary" href="index.php?route=rdvs">Retour à mes rendez-vous</a>
    </form>
</section>

<!-- Partie 3 : Fin du contenu principal et chargement du menu de navigation. -->
</main><script src="views/menu.js"></script></body></html>
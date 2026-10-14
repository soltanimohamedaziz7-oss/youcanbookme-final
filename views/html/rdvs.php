<?php
// J'inclus le fichier layout.php.
// Ce fichier contient la structure commune de mes pages, comme l'en-tête et la navigation.
require __DIR__ . "/../layout.php";
?>

<?php
// Je vérifie si une notification doit être affichée.
// Par exemple, après la modification ou l'annulation d'un rendez-vous.
if (!empty($notification)):
?>

    <!--
        J'affiche un message à l'utilisateur.
        role="status" permet aux outils d'accessibilité de reconnaître ce message.
        La couleur de fond dépend du type de notification :
        vert clair pour un succès et rouge clair pour une erreur.
    -->
    <div role="status" style="padding:14px 18px;margin:14px 0;border-radius:8px;background:<?= $notification['type'] === 'success' ? '#e6f4ea' : '#fde8e8' ?>;color:#222;">

        <!--
            J'utilise e() pour sécuriser l'affichage du message.
            Cela permet notamment d'éviter d'interpréter du HTML ou du JavaScript
            qui serait présent dans le texte.
        -->
        <?= e($notification['message']) ?>

    </div>

<?php endif; ?>


<!--
    Cette section contient le titre de la page et le bouton pour ajouter un rendez-vous.
-->
<section class="page-header">

    <div>

        <h1>
            <?php
            // J'adapte le titre selon le type de compte connecté.
            // Un soignant voit les rendez-vous de ses patients.
            // Un patient voit ses propres rendez-vous.
            ?>
            <?= is_soignant() ? "Tous mes rendez-vous patients" : "Mes rendez-vous" ?>
        </h1>

        <p>
            <?php
            // J'adapte également le texte de présentation selon le type de compte.
            ?>
            <?= is_soignant() ? "Tous les rendez-vous de mon agenda, passés et à venir." : "Mon historique et mes prochains rendez-vous." ?>
        </p>

    </div>

    <!--
        Ce lien permet d'accéder à la page d'ajout d'un rendez-vous.
        La route rdv-add est traitée par le routeur de mon application.
    -->
    <a
        class="button"
        href="index.php?route=rdv-add">
        + Ajouter
    </a>

</section>


<?php
// Je vérifie si la liste des rendez-vous est vide.
// empty() permet de vérifier si le tableau ne contient aucun rendez-vous.
if (empty($rdvs)):
?>

    <!--
        Si aucun rendez-vous n'est disponible,
        j'affiche un message pour informer l'utilisateur.
    -->
    <section class="empty-card">

        <h2>
            Aucun rendez-vous
        </h2>

        <p>
            Aucun rendez-vous n'est enregistré.
        </p>

    </section>

<?php else: ?>

    <!--
        Si des rendez-vous existent, je crée une section pour afficher les cartes.
    -->
    <section class="cards">

        <?php
        // foreach parcourt le tableau $rdvs.
        // À chaque passage, la variable $rdv représente un rendez-vous.
        foreach ($rdvs as $rdv):
        ?>

            <!--
                Chaque rendez-vous est affiché dans une carte.
                La classe card permet d'appliquer le style défini dans le CSS.
            -->
            <article class="card">

                <h2>
                    <!--
                        J'affiche le prénom et le nom associés au rendez-vous.
                        e() protège les valeurs affichées.
                    -->
                    <?= e($rdv["prenom"]) ?>
                    <?= e($rdv["nom"]) ?>
                </h2>

                <?php
                // Je vérifie si le compte connecté est celui d'un soignant.
                // Si c'est le cas, j'affiche aussi les informations du patient.
                ?>
                <?php if (is_soignant()): ?>
                    <p>
                        <strong>Patient :</strong>
                        <?= e(($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '')) ?>
                        (<?= e($rdv['patient_mail'] ?? '') ?>)
                    </p>
                <?php endif; ?>

                <p>
                    <strong>
                        Discipline :
                    </strong>

                    <!--
                        J'affiche la discipline du soignant.
                        ?? "" permet d'utiliser une chaîne vide si la valeur est absente.
                    -->
                    <?= e($rdv["discipline"] ?? "") ?>
                </p>

                <p>
                    <strong>
                        Date :
                    </strong>

                    <!-- J'affiche la date du rendez-vous. -->
                    <?= e($rdv["date"]) ?>
                </p>

                <p>
                    <strong>
                        Description :
                    </strong>

                    <!-- J'affiche la description du rendez-vous si elle existe. -->
                    <?= e($rdv["description"] ?? "") ?>
                </p>

                <!--
                    Ce lien permet de modifier le rendez-vous sélectionné.
                    L'identifiant id permet de retrouver le bon rendez-vous.
                    (int) convertit cette valeur en nombre entier.
                -->
                <a class="button" href="index.php?route=rdv-edit&id=<?= (int)$rdv['id'] ?>">Modifier</a>

                <!--
                    Ce formulaire permet de demander l'annulation du rendez-vous.
                    La méthode POST sert à envoyer les données au serveur.
                    Avant l'envoi, la fonction JavaScript demande une confirmation.
                -->
                <form
                    method="POST"
                    action="index.php?route=rdv-delete"
                    onsubmit="return confirmerAnnulationRdv(this);">

                    <!--
                        J'envoie un jeton CSRF avec le formulaire.
                        Il permet au serveur de vérifier que la demande respecte
                        la protection prévue par l'application contre certaines
                        requêtes envoyées à l'insu de l'utilisateur.
                    -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>">

                    <!--
                        Ce champ caché transmet l'identifiant du rendez-vous à annuler.
                        Il n'est pas visible sur la page, mais il est envoyé au serveur.
                    -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $rdv["id"] ?>">

                    <!--
                        Je prépare une date lisible pour la confirmation JavaScript.
                        date() formate la date et strtotime() transforme la date
                        en valeur utilisable par date().
                    -->
                    <input type="hidden" name="rdv_label" value="<?= e(date('d/m/Y à H:i', strtotime($rdv['date']))) ?>">

                    <!--
                        Ce bouton permet de demander l'annulation.
                        La classe danger sert à lui donner un style visuel d'action risquée.
                        aria-label fournit une description supplémentaire pour l'accessibilité.
                    -->
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
    // Cette fonction affiche une fenêtre de confirmation avant l'annulation.
    // Elle évite que l'utilisateur envoie le formulaire par erreur.
    function confirmerAnnulationRdv(formulaire) {

        // Je récupère la date du rendez-vous dans le champ caché du formulaire.
        const date = formulaire.elements['rdv_label'].value;

        // window.confirm affiche une fenêtre avec deux choix :
        // confirmer ou annuler.
        const ok = window.confirm('Annuler uniquement le rendez-vous du ' + date + ' ?\nLe créneau sera de nouveau disponible.');

        // Si l'utilisateur refuse, je bloque l'envoi du formulaire.
        if (!ok) return false;

        // Je récupère le bouton d'envoi du formulaire.
        const bouton = formulaire.querySelector('button[type="submit"]');

        // Si le bouton existe, je le désactive pour éviter plusieurs clics.
        if (bouton) {
            bouton.disabled = true;

            // Je change le texte pour indiquer que l'annulation est en cours.
            bouton.textContent = 'Annulation en cours…';
        }

        // Je retourne true pour autoriser l'envoi du formulaire au serveur.
        return true;
    }
</script>

<!--
    Je charge le fichier JavaScript qui gère le menu de navigation.
-->
<script src="views/menu.js"></script>

</body>

</html>
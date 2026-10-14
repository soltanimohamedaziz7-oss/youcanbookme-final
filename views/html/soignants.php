<?php
// 
// PAGE LISTE DES SOIGNANTS
// Cette page permet de rechercher des soignants,
// de consulter leurs informations et de les ajouter en favoris.
//

// J'inclus le fichier layout.php.
// Il contient la structure commune de mes pages.
require __DIR__ . "/../layout.php";
?>

<!-- Cette section contient le titre et la présentation de la page. -->
<section class="page-header">

    <div>

        <h1>
            <!-- Titre principal de la page. -->
            Mes soignants
        </h1>

        <p>
            <!-- J'explique à l'utilisateur ce qu'il peut faire sur cette page. -->
            Recherchez un soignant et ajoutez-le en favori.
        </p>

    </div>



</section>

<!-- 
     RECHERCHE
     je peut rechercher par nom, prénom ou discipline.
     Exemple : Dupont, Jean, Dentiste, Généraliste...
     
     Le formulaire permet de rechercher un professionnel de santé.
     La méthode GET transmet la recherche dans l'adresse de la page.
-->

<form
    method="GET"
    action="index.php"
    class="search-form"
>

    <!--
        Ce champ caché indique au routeur quelle page doit traiter la recherche.
        L'utilisateur ne voit pas ce champ.
    -->
    <input
        type="hidden"
        name="route"
        value="soignants-search"
    >

    <!--
        Champ de recherche.
        L'utilisateur peut saisir un nom, un prénom ou une discipline.
        La valeur déjà recherchée reste affichée après l'envoi du formulaire.
        ?? "" permet d'utiliser une chaîne vide si aucune recherche n'existe.
    -->
    <input
        type="search"
        name="recherche"
        placeholder="Nom, prénom ou discipline..."
        value="<?= e($_GET["recherche"] ?? "") ?>"
    >

    <!-- Ce bouton envoie la recherche au serveur. -->
    <button
        class="button"
        type="submit"
    >
        Rechercher
    </button>

</form>

<!--
    Cette section regroupe les raccourcis vers d'autres fonctionnalités.
-->
<div class="page-actions">

    <?php
    // Je vérifie que l'utilisateur connecté n'est pas un soignant.
    // Si c'est un patient, je lui propose d'accéder à ses favoris.
    if (!is_soignant()):
    ?>

    <a
        class="button secondary"
        href="index.php?route=mes-soignants"
    >
        ❤️ Mes favoris
    </a>

    <?php endif; ?>

    <!--
        Ce lien permet d'accéder directement au formulaire
        de prise de rendez-vous.
    -->
    <a
        class="button secondary"
        href="index.php?route=rdv-add"
    >
        📅 Prendre un RDV
    </a>

</div>

<?php
// Je vérifie si la liste des soignants est vide.
// Si aucun résultat n'est disponible, j'affiche un message adapté.
if (empty($soignants)):
?>

    <!-- Bloc affiché lorsqu'aucun soignant n'est trouvé. -->
    <section class="empty-card">

        <h2>
            Aucun soignant trouvé
        </h2>

        <?php
        // Je vérifie si l'utilisateur a effectué une recherche.
        // Si oui, je peux lui indiquer le terme recherché.
        if (!empty($_GET["recherche"])):
        ?>

            <p>
                Aucun résultat pour :

                <strong>
                    <!-- J'affiche le texte recherché en sécurisant son affichage. -->
                    <?= e($_GET["recherche"]) ?>
                </strong>
            </p>

            <!--
                Ce lien permet de revenir à la liste générale
                sans conserver le terme de recherche.
            -->
            <a
                class="button"
                href="index.php?route=soignants"
            >
                Afficher tous les soignants
            </a>

        <?php else: ?>

            <!-- Message affiché lorsqu'aucun soignant n'est disponible. -->
            <p>
                Vous n'avez pas encore ajouté de professionnel de santé.
            </p>

            <!-- Lien permettant d'afficher la liste des soignants. -->
            <a class="button" href="index.php?route=soignants">Voir tous les soignants</a>

        <?php endif; ?>

    </section>

<?php else: ?>

    <!--
        Si des soignants sont disponibles, je les affiche dans une liste de cartes.
    -->
    <section class="cards">

        <?php
        // foreach parcourt le tableau des soignants.
        // À chaque passage, $soignant représente un professionnel de santé.
        foreach ($soignants as $soignant):
        ?>

            <!-- Chaque professionnel est affiché dans une carte. -->
            <article class="card">

                <div class="soignant-card-header">

                    <div>

                        <h2>
                            <!-- J'affiche le prénom et le nom du soignant. -->
                            <?= e($soignant["prenom"]) ?>
                            <?= e($soignant["nom"]) ?>
                        </h2>

                        <p>
                            <strong>
                                Discipline :
                            </strong>

                            <!--
                                J'affiche la spécialité du soignant.
                                Si elle n'est pas renseignée, j'affiche un texte par défaut.
                            -->
                            <?= e($soignant["discipline"] ?? "Non renseignée") ?>
                        </p>

                    </div>

                    <!-- 
                         BOUTON FAVORI
                         Le coeur est vide si le soignant n'est pas favori.
                         Il devient rouge s'il est déjà enregistré.
                         
                         Le formulaire envoie une demande au serveur
                         pour ajouter ou retirer le soignant des favoris.
                    -->

                    <?php
                    // Je masque les boutons de favoris aux comptes soignants.
                    if (!is_soignant()):
                    ?>

                    <form
                        method="POST"
                        action="index.php?route=favori-toggle"
                    >

                        <!--
                            J'envoie le jeton CSRF avec le formulaire.
                            Le serveur peut le vérifier pour protéger cette demande.
                        -->
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <!--
                            Je transmets l'identifiant du soignant concerné.
                            Le champ est caché, mais sa valeur est envoyée au serveur.
                        -->
                        <input
                            type="hidden"
                            name="soignant_id"
                            value="<?= (int) $soignant["id"] ?>"
                        >

                        <?php
                        // Je vérifie si ce soignant est déjà dans les favoris.
                        // favori_id contient son identifiant dans la liste des favoris
                        // lorsqu'une relation favorite existe.
                        if (!empty($soignant["favori_id"])):
                        ?>

                            <!-- Bouton affiché lorsque le soignant est déjà favori. -->
                            <button
                                class="favorite-button active"
                                type="submit"
                                title="Retirer des favoris"
                            >
                                ❤️
                            </button>

                        <?php else: ?>

                            <!-- Bouton affiché lorsque le soignant n'est pas encore favori. -->
                            <button
                                class="favorite-button"
                                type="submit"
                                title="Ajouter aux favoris"
                            >
                                ♡
                            </button>

                        <?php endif; ?>

                    </form>

                    <?php endif; ?>

                </div>

                <p>
                    <strong>
                        Âge :
                    </strong>

                    <!--
                        J'affiche l'âge calculé à partir de la date de naissance.
                        La fonction calculer_age() effectue ce calcul.
                    -->
                    <?= e(calculer_age($soignant["date_naissance"])) ?>
                    ans
                </p>

                <!--
                    Cette partie regroupe les actions possibles pour chaque soignant.
                -->
                <div class="actions">

                    <!--
                        Ce lien permet d'ouvrir la fiche détaillée du soignant.
                        Son identifiant est transmis dans l'adresse.
                    -->
                    <a
                        class="button secondary"
                        href="index.php?route=soignant-detail&id=<?= (int) $soignant["id"] ?>"
                    >
                        Voir
                    </a>

                    <?php
                    // Je vérifie deux conditions avant d'afficher le bouton Modifier :
                    // 1. Le compte connecté est un soignant.
                    // 2. L'identifiant du soignant correspond à celui du compte connecté.
                    //
                    // Cela permet de réserver ce lien à son propre profil professionnel.
                    if (is_soignant() && (int)$soignant['id_user'] === (int)$_SESSION['user_id']):
                    ?>

                    <a
                        class="button secondary"
                        href="index.php?route=soignant-edit&id=<?= (int) $soignant["id"] ?>"
                    >
                        Modifier
                    </a>

                    <?php endif; ?>

                    <!--
                        Ce lien ouvre le formulaire de prise de rendez-vous.
                        L'identifiant du soignant est transmis dans l'URL.
                        Cela permet de préparer le rendez-vous avec le professionnel choisi.
                    -->
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

<!-- Je charge le JavaScript qui gère le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
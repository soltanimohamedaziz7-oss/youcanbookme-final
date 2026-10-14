<?php
// J'inclus le fichier layout.php.
// Il contient la structure commune de mes pages.
require __DIR__ . "/../layout.php";
?>

<!-- Cette section contient le formulaire de création de compte. -->
<section class="auth-card">

    <h1>
        <!-- Titre de la page d'inscription. -->
        Créer un compte
    </h1>

    <?php
    // Je vérifie si une erreur doit être affichée.
    // Par exemple, si une information saisie n'est pas correcte.
    if (!empty($error)):
    ?>

        <!-- J'affiche le message d'erreur dans un bloc prévu pour cela. -->
        <div class="alert error">

            <!--
                J'utilise e() pour sécuriser l'affichage du message.
                Cela évite notamment d'interpréter du code HTML présent dans le texte.
            -->
            <?= e($error) ?>

        </div>

    <?php endif; ?>

    <!--
        Ce formulaire permet au visiteur de créer son compte.
        method="POST" permet d'envoyer les informations au serveur.
        La route register indique au routeur quelle action doit être traitée.
    -->
    <form
        method="POST"
        action="index.php?route=register"
    >

        <!--
            Ce champ caché transmet un jeton CSRF avec le formulaire.
            Il permet au serveur de vérifier la demande selon la protection CSRF
        -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <!-- Champ pour saisir le nom de l'utilisateur. -->
        <label for="nom">
            Nom
        </label>

        <input
            id="nom"
            name="nom"
            type="text"
            required
        >

        <!-- Champ pour saisir le prénom de l'utilisateur. -->
        <label for="prenom">
            Prénom
        </label>

        <input
            id="prenom"
            name="prenom"
            type="text"
            required
        >

        <!--
            Je demande la date de naissance.
            Le type date affiche un champ adapté à la saisie d'une date.
        -->
        <label for="date_naissance">
            Date de naissance
        </label>

        <input
            id="date_naissance"
            name="date_naissance"
            type="date"
            required
        >

        <!-- Champ pour saisir l'adresse e-mail. -->
        <label for="mail">
            Email
        </label>

        <input
            id="mail"
            name="mail"
            type="email"
            required
        >

        <!--
            Je propose deux types de comptes :
            patient ou soignant.
            Le choix est envoyé au serveur avec le formulaire.
        -->
        <label for="role">Type de compte</label>
        <select id="role" name="role" required><option value="patient">Patient</option><option value="soignant">Soignant</option></select>

        <!--
            Ce bloc contient les champs supplémentaires pour un soignant.
            display:none signifie que le bloc est masqué au départ.
            JavaScript peut ensuite l'afficher lorsque le visiteur choisit
            le type de compte soignant.
        -->
        <div id="champs-soignant" style="display:none">

            <!-- Liste des spécialités disponibles pour les soignants. -->
            <label for="id_discipline">Spécialité du soignant</label>

            <select id="id_discipline" name="id_discipline">

                <!-- Choix affiché par défaut. -->
                <option value="">Choisir une spécialité</option>

                <?php
                // Je parcours la liste des disciplines préparée par PHP.
                // Si aucune discipline n'est disponible, j'utilise un tableau vide.
                foreach (($disciplines ?? []) as $discipline):
                ?>

                    <!--
                        Chaque discipline devient une option de la liste.
                        La valeur envoyée au serveur est son identifiant.
                        e() sécurise l'affichage du nom de la discipline.
                    -->
                    <option value="<?= (int)$discipline['id'] ?>"><?= e($discipline['nom']) ?></option>

                <?php endforeach; ?>

            </select>

            <!-- Champ pour saisir l'adresse professionnelle du soignant. -->
            <label for="adresse">Adresse du cabinet</label>

            <input id="adresse" name="adresse" type="text" placeholder="Adresse professionnelle">

        </div>

        <!--
            Je demande un mot de passe.
            type="password" masque les caractères pendant la saisie.
            minlength="8" impose une longueur minimale de huit caractères
            dans le navigateur.
        -->
        <label for="password">
            Mot de passe
        </label>

        <input
            id="password"
            name="password"
            type="password"
            minlength="8"
            required
        >

        <!--
            Ce bouton envoie le formulaire pour demander la création du compte.
            Le serveur doit ensuite vérifier les données et enregistrer le compte.
        -->
        <button
            class="button"
            type="submit"
        >
            Créer mon compte
        </button>

    </form>

    <!--
        Cette partie permet aux personnes qui possèdent déjà un compte
        d'accéder directement à la page de connexion.
    -->
    <p class="center">
        Déjà inscrit ?

        <a href="index.php?route=login">
            Se connecter
        </a>
    </p>

</section>

</main>

<!-- Je charge le fichier JavaScript utilisé pour le menu de navigation. -->
<script src="views/menu.js"></script>

</body>
</html>
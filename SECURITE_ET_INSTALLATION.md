# Sécurité du projet MVC / MySQLi

## Installation
1. Copiez le dossier `youcanbookme` dans `C:\xampp\htdocs\youcanbookme1`.
2. Le fichier `config/config-private.php` est fourni pour XAMPP local. Modifiez ses paramètres pour votre environnement et **ne le publiez jamais dans Git**.
3. `config/config-sample.php` sert uniquement de modèle sans secret réel. Pour déployer, gardez `config-private.php` hors du répertoire public si possible.
4. Utilisez `http://localhost/youcanbookme1/` sur XAMPP local. En production, activez HTTPS.
5. Conservez la base de données existante. **Ne réimportez pas le SQL** si elle contient déjà vos rendez-vous.

## Mesures présentes
- **MySQLi et requêtes préparées** : `mysqli_prepare()`, `mysqli_stmt_bind_param()` et `mysqli_stmt_execute()` séparent les valeurs du SQL. Une requête sans valeur variable n'a pas besoin de `bind_param()`.
- **Mots de passe** : `password_hash()` à l'inscription et `password_verify()` à la connexion. Les anciens mots de passe enregistrés en clair ne peuvent pas être vérifiés ainsi et doivent être réinitialisés.
- **Sessions et rôles** : `$_SESSION`, régénération de l'identifiant après connexion et contrôle des routes privées. Les opérations sur les rendez-vous vérifient aussi leur propriétaire.
- **XSS** : `e()` appelle `htmlspecialchars()` avec `ENT_QUOTES` et `UTF-8` avant d'afficher des valeurs dans le HTML.
- **CSRF** : les formulaires de modification envoient un jeton vérifié côté serveur.
- **Configuration privée** : `config-private.php` contient les identifiants, `config-sample.php` montre les paramètres attendus.

## Limites
La syntaxe PHP a été vérifiée, mais les tests de connexion et de réservation réels nécessitent votre serveur MySQL/XAMPP. Un audit de sécurité complet en production doit aussi contrôler les permissions Apache et les autres fichiers publics.

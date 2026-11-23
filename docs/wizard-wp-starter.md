# Journal et wizard : mise en place de wp-starter (WordPress + OVH)

Dernière mise à jour : 2026-11-23
Public : le développeur, et tout agent (Claude Code ou autre) qui doit comprendre ce qui a été fait, ou le refaire.

## Règles pour un agent qui lit ce fichier

- Ne devine pas. Les points marqués « à confirmer » n'ont pas été vérifiés.
- Ne demande jamais de secret (mot de passe, token, contenu d'un `.env`) et n'en écris aucun dans un fichier versionné ou dans le chat.
- Avant toute action dans l'espace OVH (DNS, sites, bases de données) : décris l'action et attends la confirmation de l'utilisateur.
- Procède une étape à la fois et vérifie le résultat avant de passer à la suivante.
- Les valeurs réelles (identifiants de base, mots de passe, tokens, clés) ne sont volontairement pas consignées ici.

## Objectif

Produire des sites WordPress simples pour des clients, avec :
1. développement local dans VS Code ;
2. IA dans le développement (Claude Code) ;
3. versionning sur GitHub ;
4. une solution peu chère et migrable ;
5. une édition de contenu simple pour le client.

Premier client prévu : un site de réservation de séances avec paiement en ligne (Mollie envisagé). Ce volet n'est pas encore réalisé.

## Décisions d'architecture

| Sujet | Décision | Raison |
|---|---|---|
| Édition des pages | Thème à blocs natif (`theme.json`, templates, parts, patterns), sans page builder | Tout est en fichiers versionnés, adapté à Claude Code. Bricks stockait les mises en page en base de données. Bricks reste une option si un client veut plus de liberté visuelle. |
| Structure du projet | Type Bedrock : `web/` (racine publique), `vendor/`, `wp-config.php` et `.env` à la racine du projet | Le cœur WordPress est une dépendance Composer (`roots/wordpress`), la configuration passe par `.env` (`vlucas/phpdotenv`) |
| Code réutilisable | Plugin `wp-core`, repo séparé, package Composer privé | Se propage avec `composer update` après un tag. Actuellement une coquille (stubs vides, tag `v0.1.0`). |
| Repos GitHub | `wp-core`, `wp-starter`, `wp-workspace` (compte `Loic9941`) | `wp-workspace` doit réunir les deux autres par submodules. Assemblage : à confirmer. |
| Propagation | `wp-core` : via Composer. `wp-starter` : copié (template), ne se propage pas aux sites déjà livrés | Un site livré reste figé |
| Hébergement | OVHcloud mutualisé Pro, 12 mois, « Sans module pré-installé » | Prix, SFTP, racine web configurable |
| Déploiement | Manuel, par SFTP avec WinSCP. Pas de CI pour l'instant | Valider d'abord l'architecture à la main |
| Pages du site | Entièrement en code : `templates/page-{slug}.html` + patterns pour le contenu, mu-plugin `site-pages.php` pour créer les pages en base, menu en dur dans `parts/` | Le site arrive complet en ligne par simple envoi de fichiers, sans saisie dans l'admin ni import de base. Limite : le client ne modifie pas ces pages depuis l'éditeur. Le mécanisme pourra passer dans `wp-core` quand la liste des pages sera configurable par site. |

Écarté pour l'instant : Railway (conteneurs, plus d'exploitation à ta charge), WooCommerce dans le starter (à mettre dans le repo du client), CI/CD complète (prématurée), thème parent/enfant (utile à partir de plusieurs clients).

## Chronologie des étapes réalisées

### Étape 1 : repos GitHub
- Trois repos vides créés : `wp-core`, `wp-starter`, `wp-workspace`.
- `wp-core` et `wp-starter` clonés dans un dossier local `wp/`.
- Identité Git configurée (`user.name`, `user.email`).

### Étape 2 : `wp-core`
- Plugin WordPress packagé comme dépendance Composer (type `wordpress-plugin`, compatible `composer/installers`), avec des fichiers `inc/` vides et commentés.
- Le champ `name` de son `composer.json` doit être en minuscules : `loic9941/wp-core`.
- Poussé sur `main`, puis tag `v0.1.0`.

### Étape 3 : outils sous Windows
- WSL2 et Docker Desktop déjà présents. Docker Desktop doit être lancé avant tout `ddev`.
- DDEV installé avec `winget install -e --id DDEVFoundation.DDEV` (l'identifiant `DDEV.DDEV` ne fonctionnait pas).
- PHP et Composer ne sont pas installés sur Windows : tout passe par `ddev composer ...`.

### Étape 4 : `wp-starter`
- `composer.json` : dépôt de type `vcs` vers `wp-core`, dépendances `loic9941/wp-core: ^0.1`, `composer/installers`, `roots/wordpress`, `vlucas/phpdotenv`.
- Dépôt privé : un token GitHub fine-grained (permission Contents en lecture seule, limité à `wp-core`) est demandé par Composer au premier `ddev composer`. Il est stocké dans le conteneur DDEV. Sa persistance après recréation du conteneur : à confirmer.
- `.gitignore` : `vendor/`, cœur WordPress (`web/wp/`), `web/wp-content/plugins/wp-core/`, `.env`, `web/wp-config-ddev.php`, uploads.
- Un dépôt de type `path` vers `../wp-core` avait été commité : il cassait l'installation hors de la machine du développeur. Il a été retiré. Pour itérer sur `wp-core`, utiliser `dev-main`.
- Thème renommé `boilerplate-theme` → `starter-theme` (dossier, `Theme Name`, `Text Domain`, constantes dans `functions.php`).
- `CLAUDE.md` doit s'écrire en majuscules dans Git. Sous Windows, passer par un nom intermédiaire : `git mv claude.md tmp.md` puis `git mv tmp.md CLAUDE.md`.

### Étape 5 : thème à blocs
- `theme.json` (version 3), templates (`index`, `page`, `single`, `404`), parts (`header`, `footer`).
- Trois patterns dans la catégorie « Starter » : hero, points forts, appel à l'action.
- Contrastes contrôlés (hero : blanc sur `#1f3a5f` ; bouton d'accent à 4,74, juste au-dessus du seuil AA).
- Affichés dans Apparence → Éditeur → Patterns. Test avec un compte Éditeur : à confirmer.

### Étape 6 : site local
- `ddev start`, `ddev composer install`, `ddev launch`. Le site s'affiche avec le thème.
- L'avertissement DDEV sur `wp-config.php` / `wp-config-ddev.php` est sans gravité.

### Étape 7 : OVH
- Commande : offre Pro, 12 mois, « Sans module pré-installé » (ne pas choisir le module WordPress en un clic, il entrerait en conflit avec la structure Composer).
- Base de données MySQL créée dans l'onglet Bases de données (relever nom, utilisateur, mot de passe, hôte depuis la fiche de la base).
- FTP-SSH : SFTP actif (port 22), SSH désactivé (« SFTP only »).
- Un site OVH a été ajouté dans Mes sites pour le sous-domaine de test (voir les pièges ci-dessous).

### Étape 8 : déploiement manuel
- Build de production : `ddev composer install --no-dev --optimize-autoloader`.
- Dossier `wp-starter` créé sur le serveur, à côté de `www`.
- Envoyé avec WinSCP : `vendor/`, `web/`, `wp-config.php` (celui de la racine).
- Non envoyé : `.env` local, `.ddev/`, `.git/`, `CLAUDE.md`, `README.md`, `composer.lock`, `wp-cli.yml`, `web/wp-config-ddev.php`, uploads locaux.
- `.env` du serveur créé à la main : `WP_ENV=production`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`, `DB_PREFIX`, `WP_HOME`, plus les huit clés et salts générés par un script PowerShell.
- Dossier racine du site OVH réglé sur `./wp-starter/web`.
- Installation de WordPress terminée dans le navigateur.
- HTTPS : certificat valide pour le sous-domaine de test (cadenas confirmé).

### Étape 9 : pages en code (local)
- `templates/front-page.html` : accueil composé des patterns hero, points forts et appel à l'action.
- Patterns `about-intro` et `contact-infos`, contenu fictif ; templates `page-a-propos.html` et `page-contact.html`.
- mu-plugin `web/wp-content/mu-plugins/site-pages.php` : crée les pages `a-propos` et `contact` si elles manquent, règle les permaliens sur `/%postname%/`, une seule fois par `SITE_PAGES_VERSION`. Testé : pas de doublon à la relance.
- Menu écrit en dur (blocs `navigation-link`) dans le header et le footer ; boutons du hero et de l'appel à l'action reliés à `/a-propos/` et `/contact/`.
- `WP_DEVELOPMENT_MODE=theme` en local (`wp-config.php`, si `WP_ENV=development`).
- Thème passé en version `0.2.0`.
- Déploiement en ligne de cette étape : à faire.

## Pièges rencontrés et solutions

| Piège | Cause | Solution |
|---|---|---|
| `ddev` introuvable | DDEV non installé, ou mauvais identifiant winget | `winget search ddev`, puis `DDEVFoundation.DDEV` |
| « Docker error » | Docker Desktop non lancé | Le démarrer, attendre « Running » |
| `Rename-Item` refuse le chemin complet | Le deuxième argument doit être un nom seul | `Rename-Item chemin\ancien nouveau` |
| SFTP : `Permission denied` puis `Connection timed out` | Plusieurs mots de passe refusés d'affilée, adresse IP probablement bloquée temporairement (hypothèse) | Cesser les essais, changer le mot de passe SFTP dans OVH, tester depuis un autre réseau (partage de connexion) |
| `NXDOMAIN` sur le sous-domaine | Les enregistrements A/AAAA avaient été créés sous le nom doublé `test.<domaine>.<domaine>` | Vérifier avec `nslookup <nom> dns109.ovh.net` (serveur DNS d'OVH, sans cache). Supprimer les deux lignes et les recréer avec le sous-domaine seul (`test`) |
| Certificat HTTPS invalide | Le site OVH avait été rattaché au nom doublé | Recréer le site dans Mes sites, rattacher le bon nom (le domaine ne doit apparaître qu'une fois), attendre la génération du certificat |
| `composer.lock` périmé | Lock non régénéré après changement de `composer.json` | `ddev composer update`, puis commit du lock |
| `CLAUDE.md` en minuscules dans Git | Windows ignore la casse | Renommer via un nom intermédiaire |
| Caractères bizarres dans PowerShell | Encodage de la console | Sans conséquence, le fichier est correct |
| Nouveau pattern absent (éditeur, `wp:pattern`) | Depuis WordPress 6.4, la liste des fichiers de `patterns/` est mise en cache et n'est relue qu'au changement de `Version` du thème | En local : `WP_DEVELOPMENT_MODE=theme`. En production : incrémenter `Version` dans `style.css` à chaque ajout de pattern |

## État au 2026-11-23

Fait : repos créés, `wp-core` v0.1.0, `wp-starter` avec thème à blocs, site local DDEV, hébergement OVH Pro actif, base de données, SFTP, sous-domaine de test avec HTTPS, déploiement manuel réussi, WordPress installé.

À confirmer ou non fait :
- `WP_HOME` en `https://` dans le `.env` du serveur ;
- mot de passe administrateur changé après le passage en HTTPS ;
- thème `starter-theme` et plugin `wp-core` activés sur le site en ligne ;
- README de `wp-starter` commité et poussé ;
- `wp-starter` marqué comme « Template repository » sur GitHub ;
- assemblage de `wp-workspace` (submodules) ;
- test du rôle Éditeur ;
- éventuel assombrissement de la couleur d'accent (marge de contraste du bouton) ;
- renouvellement automatique OVH (décision à prendre avant l'échéance) ;
- SSH et déploiement automatique (CI) : non faits.

## Prochaines étapes envisagées

1. Fin du durcissement du site de test (HTTPS dans `.env`, mot de passe).
2. Décider de la réservation avec paiement : plugin de réservation avec add-on Mollie, ou WooCommerce avec le plugin Mollie officiel. La question qui tranche : le client vendra-t-il autre chose que des séances ?
3. Créer le repo du premier client à partir de `wp-starter` (voir `guide-nouveau-client.md`).
4. Plus tard : activer SSH chez OVH, une clé de déploiement, et un workflow GitHub Actions pour remplacer WinSCP.

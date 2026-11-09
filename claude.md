# CLAUDE.md — wp-starter

Boilerplate WordPress réutilisable, point de départ pour chaque nouveau
site client. Cloné via GitHub template (ou cloné + réinitialisé en git)
à chaque nouveau projet.

## Stack
- WordPress + Bricks Builder (thème)
- PHP, Composer pour la gestion de dépendances
- DDEV pour l'environnement local
- Déploiement : GitHub Actions + rsync via SSH

## Dépendances externes
wp-core (https://github.com/Loic9941/wp-core) est un package Composer
séparé, installé via composer.json, JAMAIS copié ni dupliqué dans ce repo.
Le dossier wp-content/plugins/wp-core/ est généré par `composer install`
et doit rester gitignored.

En dev local uniquement (jamais en CI/prod), composer.json peut utiliser
un repository de type "path" pointant vers un wp-core cloné en local
(symlink), pour itérer sans push/tag intermédiaire. En CI/prod, c'est
toujours le repository "vcs" GitHub avec une contrainte de version taguée
(ex: ^0.1) qui fait foi.

## Architecture
- wp-content/themes/starter-theme/ : thème minimal, config Bricks
  uniquement (pas de logique métier ici)
- wp-content/plugins/ : trackable pour d'éventuels plugins spécifiques
  à un client ; wp-core y est installé par Composer, pas versionné
- wp-content/themes/starter-theme/bricks/kit-export.json : kit de
  sections Bricks réutilisable, versionné, réimporté à chaque nouveau
  client (flux à sens unique — les anciens projets ne sont jamais
  resynchronisés avec un kit mis à jour)

## Conventions
- Tout le code custom réutilisable (CPT, champs ACF, restrictions de
  rôle) va dans wp-core, jamais dans ce repo
- wp-config.php n'est jamais versionné ; licence Bricks injectée via
  variable d'environnement (constante BRICKS_LICENSE_KEY, lue depuis .env)
- Un script scripts/new-client.sh doit permettre de provisionner un
  nouvel environnement local en une commande (clone, DDEV, wp-cli install,
  import du kit Bricks)
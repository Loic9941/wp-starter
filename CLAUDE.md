# CLAUDE.md — wp-starter

Boilerplate WordPress réutilisable, point de départ pour chaque nouveau
site client. Cloné via GitHub template (ou cloné + réinitialisé en git)
à chaque nouveau projet.

## Stack
- WordPress, thème à blocs (Full Site Editing)
- PHP, Composer pour la gestion de dépendances
- DDEV pour l'environnement local
- Déploiement : GitHub Actions + rsync via SSH

## Dépendances externes
wp-core (https://github.com/Loic9941/wp-core) est un package Composer
séparé, installé via composer.json, JAMAIS copié ni dupliqué dans ce repo.
Le dossier web/wp-content/plugins/wp-core/ est généré par `composer install`
et doit rester gitignored.

Le cœur WordPress est lui aussi une dépendance Composer (roots/wordpress),
installé dans web/wp/ et jamais versionné. Mise à jour de WordPress :
`ddev composer update roots/wordpress`.

En dev local uniquement (jamais en CI/prod), composer.json peut utiliser
un repository de type "path" pointant vers un wp-core cloné en local
(symlink), pour itérer sans push/tag intermédiaire. En CI/prod, c'est
toujours le repository "vcs" GitHub avec une contrainte de version taguée
(ex: ^0.1) qui fait foi.

## Architecture
```
wp-starter/
├── composer.json / vendor/   hors docroot
├── wp-config.php             config versionnée, sans secret (lit .env)
├── .env / .env.example       secrets (.env non versionné)
└── web/                      docroot (DDEV : docroot: web)
    ├── index.php             charge wp/wp-blog-header.php
    ├── wp-config.php         stub → ../wp-config.php
    ├── wp/                   cœur WordPress (Composer, gitignored)
    └── wp-content/           thèmes, plugins, uploads
```
- Admin : /wp/wp-admin/ (WP_SITEURL = WP_HOME + /wp)
- Aucun thème ni plugin fourni par WordPress (twentytwenty*, Akismet…) :
  tout passe par Composer (wpackagist si besoin)
- web/wp-content/themes/starter-theme/ : thème à blocs minimal, présentation
  uniquement (pas de logique métier ici)
  - theme.json (schema v3) : source unique des design tokens (palette,
    tailles de police fluides, échelle d'espacements 20→70, largeurs
    720px / 1200px) et des styles globaux
  - templates/ : index, page, single, 404 (HTML de blocs)
  - parts/ : header et footer (déclarés dans theme.json → templateParts)
  - patterns/ : patterns PHP (hero, features, cta), catégorie "starter"
    enregistrée dans functions.php ; remplacent l'ancien kit Bricks
  - functions.php : volontairement minimal (textdomain, wp-block-styles,
    catégorie de patterns, enqueue de style.css)
- web/wp-content/plugins/ : trackable pour d'éventuels plugins spécifiques
  à un client ; wp-core y est installé par Composer, pas versionné

## Conventions
- Tout le code custom réutilisable (CPT, champs ACF, restrictions de
  rôle) va dans wp-core, jamais dans ce repo
- Thème : blocs core WordPress uniquement, aucune valeur en dur dans
  templates/parts/patterns — toujours les presets de theme.json
  (`var:preset|spacing|40`, slugs de couleur/taille)
- Textes des patterns en français, via les fonctions de traduction
  (esc_html_e, __) avec le text domain `starter-theme`
- Contraste : toute combinaison texte/fond doit passer WCAG AA (≥ 4,5)
- WordPress ≥ 6.6 requis (theme.json v3)
- wp-config.php est versionné mais ne contient aucun secret : base de
  données, clés et salts viennent de l'environnement ou de .env
  (jamais versionné ; modèle dans .env.example)
- Un script scripts/new-client.sh doit permettre de provisionner un
  nouvel environnement local en une commande (clone, DDEV, wp-cli install)
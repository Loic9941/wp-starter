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

Seul le repository "vcs" GitHub est déclaré dans composer.json (pas de
repository "path"). Pour itérer sur wp-core en dev local sans tag
intermédiaire : pousser sur la branche main de wp-core puis, en local
uniquement, `ddev composer require loic9941/wp-core:dev-main` (puis
`ddev composer update loic9941/wp-core` à chaque push). Ne jamais
committer cette contrainte : en CI/prod, c'est toujours une version
taguée (ex: ^0.1) qui fait foi.

## Architecture
```
wp-starter/
├── composer.json / vendor/   hors docroot
├── wp-config.php             config versionnée, sans secret (lit .env)
├── .env / .env.example       secrets (.env non versionné)
├── wp-cli.yml                path: web/wp (sinon WP-CLI ne trouve pas WordPress)
└── web/                      docroot (DDEV : docroot: web)
    ├── index.php             charge wp/wp-blog-header.php
    ├── wp-config.php         stub → ../wp-config.php, puis ABSPATH et
    │                         require wp-settings.php (exigé par WP-CLI)
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
  - templates/ : index, page, single, 404, front-page (accueil),
    page-a-propos et page-contact (HTML de blocs)
  - parts/ : header et footer (déclarés dans theme.json → templateParts),
    menu écrit en dur (navigation-link), pas de menu en base
  - patterns/ : patterns PHP (hero, features, cta, about-intro,
    contact-infos), catégorie "starter" enregistrée dans functions.php
  - functions.php : volontairement minimal (textdomain, wp-block-styles,
    catégorie de patterns, enqueue de style.css)
- web/wp-content/plugins/ : trackable pour d'éventuels plugins spécifiques
  à un client ; wp-core y est installé par Composer, pas versionné
- web/wp-content/mu-plugins/site-pages.php : crée les pages déclarées
  (slug → titre) si elles manquent et règle les permaliens sur
  /%postname%/. Chargé d'office, rien à activer.

## Pages en code
- Le contenu d'une page vit dans templates/page-{slug}.html + patterns ;
  la page en base n'est qu'une coquille vide créée par site-pages.php
- Ajouter une page : template, slug dans site_pages_list(), puis
  incrémenter SITE_PAGES_VERSION
- Toute modification faite dans l'éditeur de site (templates, parts) est
  stockée en base et remplace le fichier : à réinitialiser le cas échéant
- Cache des patterns : en production, un nouveau pattern n'apparaît
  qu'après changement de `Version` dans style.css (et
  STARTER_THEME_VERSION dans functions.php). En local,
  WP_DEVELOPMENT_MODE=theme (wp-config.php, si WP_ENV=development)
  désactive ce cache

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

## Documentation (docs/)
- docs/wizard-wp-starter.md : journal des décisions, état actuel, pièges
  rencontrés. À lire en premier ; respecter ses règles pour les agents.
- docs/guide-nouveau-client.md : créer un site client, jusqu'à la mise en ligne
- docs/guide-mise-a-jour-site.md : déployer une mise à jour sur un site livré
# wp-starter

Point de départ des sites WordPress clients : WordPress installé par
Composer, configuration sans secret versionnée, thème à blocs
`starter-theme` et plugin [wp-core](https://github.com/Loic9941/wp-core)
(fonctionnalités communes), le tout en local avec DDEV.

```
wp-starter/
├── composer.json / vendor/   dépendances (hors docroot)
├── wp-config.php             configuration versionnée, lit .env
├── .env.example              modèle de .env (secrets, non versionné)
├── wp-cli.yml                indique à WP-CLI que WordPress est dans web/wp
└── web/                      docroot
    ├── wp-config.php         relais vers ../wp-config.php
    ├── wp/                   cœur WordPress (Composer, non versionné)
    └── wp-content/
        ├── themes/starter-theme/
        └── plugins/wp-core/  installé par Composer, non versionné
```

## Prérequis

- **Git**
- **Docker** : Docker Desktop (Windows/macOS) ou Docker Engine (Linux),
  démarré avant toute commande `ddev`
- **DDEV** ≥ 1.24 : <https://docs.ddev.com/en/stable/users/install/>
  (testé avec la v1.25.5). Pour avoir un HTTPS reconnu par le
  navigateur, lancez une fois `mkcert -install` ; sinon, utilisez les
  URLs en `http://`.
- **Accès en lecture au dépôt privé `Loic9941/wp-core`**, et un token
  GitHub pour que Composer puisse le télécharger (voir ci-dessous)

### Token GitHub pour Composer

wp-core est un dépôt privé. Sans authentification, Composer échoue :

- `ddev composer install` : `Failed to download loic9941/wp-core from
  dist: … zipball/… (HTTP/2 404)` ;
- `ddev composer update` : `Failed to clone the
  git@github.com:Loic9941/wp-core.git repository` / `Permission denied
  (publickey)`.

Attention : si un autre projet DDEV a déjà téléchargé wp-core, Composer
peut le reprendre dans son cache partagé et l'installation passe sans
token. Ça ne durera pas : configurez quand même le token.

1. Créer un token sur <https://github.com/settings/personal-access-tokens>
   (*fine-grained token*), avec accès au dépôt `Loic9941/wp-core` et la
   permission **Contents : Read-only**.
2. Le déclarer une fois pour toutes, pour tous les projets DDEV, dans
   `~/.ddev/homeadditions/.composer/auth.json` sur votre machine
   (sous Windows : `C:\Users\<vous>\.ddev\homeadditions\.composer\auth.json`) :

   ```json
   {
       "github-oauth": {
           "github.com": "github_pat_xxxxxxxxxxxx"
       }
   }
   ```

   DDEV copie `~/.ddev/homeadditions/` dans le conteneur web à chaque
   `ddev start` / `ddev restart`. **Si le projet tourne déjà, faites
   `ddev restart`** pour que le fichier soit pris en compte. Ce fichier
   contient un secret : il est hors de tout dépôt, ne le committez nulle
   part.

   Sans ce fichier, `ddev composer update` lancé dans un terminal
   demande un token et propose de l'enregistrer. `ddev composer config
   -g github-oauth…` fait la même chose. Dans les deux cas, le token est
   écrit dans `~/.composer/auth.json` **à l'intérieur du conteneur**,
   et ne survit pas à sa recréation (`ddev restart`). Il faut le
   redonner à chaque fois, et les commandes non interactives (scripts,
   CI) échouent. Pour récupérer un token saisi de cette façon et le
   rendre permanent, **dans Git Bash et non dans PowerShell**, depuis
   le dossier du projet. Dans PowerShell, `whoami` renvoie
   `DOMAINE\utilisateur` et `>` écrit un fichier que Composer ne sait
   pas lire.

   ```bash
   mkdir -p ~/.ddev/homeadditions/.composer
   ddev exec cat /home/$(whoami)/.composer/auth.json > ~/.ddev/homeadditions/.composer/auth.json
   ```

## Installation locale

Les commandes ci-dessous fonctionnent dans PowerShell comme dans un
shell Unix. Remplacez `mon-client` par le nom du projet.

```bash
# 1. Récupérer le code (ou « Use this template » sur GitHub, puis cloner)
git clone https://github.com/Loic9941/wp-starter.git mon-client
cd mon-client

# 2. Donner son propre nom au projet DDEV (URL : https://mon-client.ddev.site).
#    Obligatoire si un projet « wp-starter » existe déjà sur la machine :
#    deux projets DDEV ne peuvent pas porter le même nom.
ddev config --project-name=mon-client

# 3. Créer le fichier d'environnement
cp .env.example .env

# 4. Démarrer l'environnement
ddev start

# 5. Installer WordPress, wp-core et les dépendances PHP
ddev composer install
```

Sur un clone réalisé pour un nouveau client, réinitialisez aussi
l'historique Git si vous n'êtes pas passé par « Use this template ».

### À propos de `.env`

Sous DDEV, il n'y a rien à remplir : la base de données et l'URL sont
fournies par DDEV et les valeurs correspondantes de `.env` sont
ignorées. Les clés et salts peuvent rester vides en local (WordPress en
génère et les stocke en base). Ils sont **obligatoires** en staging et
en production : <https://api.wordpress.org/secret-key/1.1/salt/>.

### Avertissement `wp-config-ddev.php` (`ddev config` / `ddev start`)

`ddev config` et `ddev start` affichent ce message, parfois plusieurs
fois :

```
An existing user-managed wp-config.php file has been detected!
Project DDEV settings have been written to:
…/web/wp-config-ddev.php
Please comment out any database connection settings in your wp-config.php and
add the following snippet to your wp-config.php […]
```

**Il est normal : ne suivez pas ses instructions.** DDEV cherche dans
`web/wp-config.php` l'inclusion du fichier qu'il génère,
`web/wp-config-ddev.php` (base de données, `WP_HOME`, `WP_SITEURL`). Ici,
`web/wp-config.php` n'est qu'un relais vers la vraie configuration,
`wp-config.php` à la racine du projet, et c'est elle qui charge
`web/wp-config-ddev.php` quand elle tourne sous DDEV
(`IS_DDEV_PROJECT`). Ajouter le snippet proposé ne servirait à rien.

Le message `A project of type 'php' was found in …/web` affiché par
`ddev config` est lui aussi sans conséquence.

### Installer le site

```bash
ddev wp core install \
  --url=https://mon-client.ddev.site \
  --title="Mon client" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@example.com

ddev wp theme activate starter-theme
ddev wp plugin activate wp-core
```

Sous PowerShell, remplacez les `\` de fin de ligne par des `` ` `` ou
écrivez la commande sur une seule ligne.

- `--url` est enregistré tel quel, mais c'est l'URL fournie par DDEV qui
  s'applique (`WP_HOME` est défini par `web/wp-config-ddev.php`).
- `starter-theme` est déjà actif après l'installation (`WP_DEFAULT_THEME`) :
  `theme activate` répond `Warning: … already active`, ce qui est normal.
- En cas d'erreur `This does not seem to be a WordPress installation`
  ou `Strange wp-config.php file`, vérifiez que `wp-cli.yml` et
  `web/wp-config.php` n'ont pas été modifiés.

### URLs

| | URL |
|---|---|
| Site | <https://mon-client.ddev.site> |
| Administration | <https://mon-client.ddev.site/wp/wp-admin/> |

Sans `mkcert -install`, utilisez les mêmes URLs en `http://`.
`ddev describe` affiche les URLs du projet.

WordPress est installé dans le sous-dossier `/wp` : l'administration est
sous `/wp/wp-admin/`. `/wp-admin/` redirige vers cette adresse.
`ddev launch` ouvre le site, `ddev launch /wp/wp-admin/` l'administration.

## Mettre à jour les dépendances

```bash
ddev composer update roots/wordpress     # WordPress
ddev composer update loic9941/wp-core    # wp-core (dernier tag compatible)
```

Committez ensuite `composer.lock`.

## Modifier wp-core

wp-core vit dans son propre dépôt et n'est jamais modifié dans
`web/wp-content/plugins/wp-core/` (dossier régénéré par Composer). Seul
le repository `vcs` GitHub est déclaré dans `composer.json`.

1. **Développer** dans un clone de wp-core, puis pousser sur sa branche
   `main`.
2. **Tester** depuis le projet, en local uniquement, en suivant
   `dev-main` :

   ```bash
   ddev composer require loic9941/wp-core:dev-main
   # après chaque nouveau push sur main :
   ddev composer update loic9941/wp-core
   ```

   Ne committez pas `composer.json` ni `composer.lock` dans cet état.
3. **Publier** : dans wp-core, mettre à jour la version (en-tête de
   `wp-core.php`), committer, puis taguer et pousser :

   ```bash
   git tag v0.2.0
   git push origin main --tags
   ```

4. **Revenir à une version taguée** dans le projet, puis committer
   `composer.json` et `composer.lock` :

   ```bash
   ddev composer require loic9941/wp-core:^0.2
   ```

   En CI et en production, c'est toujours une version taguée qui fait foi.

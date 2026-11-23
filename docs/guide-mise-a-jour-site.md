# Guide : déployer une mise à jour sur un site client existant

Pour un site déjà en ligne (par exemple : ajouter des pages, modifier le design, mettre à jour `wp-core`). Complète `guide-nouveau-client.md` (mise en place initiale) et `wizard-wp-starter.md` (contexte). `<client>` = nom du projet, `<domaine>` = domaine du site. Aucun mot de passe ni token dans ce fichier.

## 0. Principe à comprendre d'abord

Un site WordPress a deux parties qui ne voyagent pas pareil :

| Partie | Où elle vit | Comment elle arrive en ligne |
|---|---|---|
| **Code** : thème, patterns, templates, `theme.json`, plugins, `wp-core` | Fichiers, versionnés dans Git | Envoi SFTP (WinSCP) |
| **Contenu** : pages, articles, menus, réglages, commandes, réservations | Base de données du serveur | Se crée directement sur le site en ligne. Pas de synchronisation automatique avec le local. |

Conséquence : **une page créée en local n'apparaît pas en ligne** quand on envoie les fichiers. Et le contenu que le client modifie en ligne ne doit jamais être écrasé par la base locale.

Règle simple : ne jamais envoyer ni importer la base locale vers la production une fois le site livré.

## 1. Quel cas est le mien ?

| Je veux… | Ce qui change | Procédure |
|---|---|---|
| Ajouter une page de contenu (À propos, Tarifs…) avec les blocs existants | Rien dans le code | Cas A : directement dans l'administration du site en ligne |
| Ajouter une page avec une mise en page nouvelle (nouvelle section, nouveau modèle) | Code (pattern ou template) | Cas B : développer en local, envoyer le code, puis créer la page en ligne |
| Ajouter une page entièrement en code (structure figée, aucune saisie dans l'admin) | Code (template + mu-plugin) | Cas E |
| Changer couleurs, polices, en-tête, pied de page | Code (`theme.json`, `parts/`) | Cas B |
| Ajouter ou mettre à jour un plugin | `composer.json` / dossier plugin | Cas C |
| Faire évoluer `wp-core` | Package Composer | Cas D (voir aussi la section 12 du guide nouveau client) |

## 2. Cas A : ajouter une page sans toucher au code

1. Se connecter à l'administration du site en ligne.
2. Pages → Ajouter. Insérer les patterns existants (catégorie « Starter ») et saisir le contenu.
3. Publier, puis ajouter la page au menu si nécessaire.
4. Vérifier le rendu sur ordinateur et téléphone.

Aucun envoi de fichiers, aucune version Git. C'est aussi ce que le client fera lui-même.

## 3. Cas B : nouveau design ou nouvelle section (code)

### 3.1 Développer en local
1. `ddev start` (Docker Desktop lancé).
2. Créer une branche : `git switch -c feature/<sujet>`.
3. Demander à Claude Code de créer le pattern ou template (fichiers dans `web/wp-content/themes/starter-theme/patterns/` ou `templates/`). Pour une page : un pattern dans la catégorie « Starter » permet au client de l'insérer lui-même.
4. Contrôler en local (`ddev launch`), y compris les contrastes de texte (au moins 4,5).
5. Commiter : `git add`, `git commit`, puis fusion dans `main` et `git push`.

### 3.2 Sauvegarder avant d'envoyer
- Exporter la base depuis OVH (Bases de données → sauvegarde) ou noter la dernière sauvegarde automatique.
- Télécharger la copie actuelle des fichiers modifiés (WinSCP) si le changement est risqué.

### 3.3 Envoyer uniquement ce qui a changé
Lister les fichiers modifiés depuis le dernier déploiement :

```powershell
git diff --name-only <tag ou commit du dernier déploiement> HEAD
```

Envoyer ces fichiers avec WinSCP, au même emplacement sur le serveur (dans le dossier `<client>` à côté de `www`).

Si le doute existe, envoyer le dossier du thème complet : `web/wp-content/themes/starter-theme/`.

**Ne jamais écraser sur le serveur** : `.env`, le dossier `uploads/` (images du client), `wp-config.php` si rien n'y a changé.

### 3.4 Vérifier en ligne
- [ ] Le site s'affiche, sans erreur ni page blanche.
- [ ] Le nouveau pattern apparaît dans l'éditeur (Pages → Ajouter → insérer → Starter).
- [ ] Créer la page en ligne avec ce pattern (comme au cas A).
- [ ] Tester sur téléphone.
- [ ] Console du navigateur sans erreur grave.

### 3.5 Marquer la version
```powershell
git tag deploy-AAAA-MM-JJ
git push --tags
```
Cela donne le point de départ du prochain `git diff` et permet de revenir en arrière.

## 4. Cas C : plugins

- Plugin gratuit déclaré dans `composer.json` : `ddev composer update <paquet>`, tester en local, commiter `composer.lock`, puis lancer le build de production (section 5) et envoyer le dossier du plugin concerné et `vendor/` si l'autoload a changé.
- Plugin payant (installation manuelle) : envoyer son dossier dans `web/wp-content/plugins/`, noter la nouvelle version dans le README du client.
- Après envoi : ouvrir Extensions dans l'administration, activer ou mettre à jour si WordPress le demande, tester la fonction concernée.
- Plugin de paiement ou de réservation : toujours tester une réservation en mode test après la mise à jour.

## 5. Build de production (quand `vendor/` change)

À faire si `composer.json` ou `composer.lock` ont changé (plugin, `wp-core`, WordPress) :

```powershell
ddev composer install --no-dev --optimize-autoloader
```

Envoyer ensuite `vendor/` et les dossiers de `web/` concernés. Puis **remettre le local en mode normal** :

```powershell
ddev composer install
```

Cas particulier : mise à jour de WordPress lui-même (`roots/wordpress`) : le dossier `web/wp/` est à renvoyer en entier. Le faire hors des heures de forte activité du client, avec sauvegarde préalable.

## 6. Cas D : mise à jour de `wp-core`

1. Publier la nouvelle version dans `wp-core` (tag `vX.Y.Z`).
2. Dans le repo du client : `ddev composer update loic9941/wp-core`.
3. Tester en local, commiter `composer.lock`.
4. Build de production (section 5), puis envoyer `web/wp-content/plugins/wp-core/` et `vendor/`.
5. Vérifier que le plugin est toujours actif.

Rappel : en `0.x`, la contrainte `^0.1` n'accepte pas `0.2.0`.

## 6 bis. Cas E : page entièrement en code

Le contenu vient d'un template du thème ; la page en base est créée automatiquement par `web/wp-content/mu-plugins/site-pages.php` à la première visite après l'envoi.

1. En local : créer `templates/page-<slug>.html` (et ses patterns si besoin).
2. Dans `site-pages.php` : ajouter `'<slug>' => '<Titre>'` dans `site_pages_list()`, puis incrémenter `SITE_PAGES_VERSION`.
3. Ajouter le lien dans `parts/header.html` (et `parts/footer.html`).
4. Si un pattern a été ajouté : incrémenter `Version` dans `style.css` et `STARTER_THEME_VERSION` dans `functions.php` (sinon le pattern reste invisible en ligne, voir section 9).
5. Contrôler en local, commiter.
6. Envoyer avec WinSCP le dossier du thème et `web/wp-content/mu-plugins/`.
7. Visiter le site une fois, puis ouvrir `/<slug>/`. En cas de 404 : Réglages → Permaliens → Enregistrer (le `.htaccess` n'a pas pu être écrit).

Une page existante n'est jamais modifiée ni supprimée par le mu-plugin. Le client ne peut pas modifier le texte de ces pages depuis l'éditeur de page : réserver ce cas aux pages dont la structure ne change pas.

## 7. Retour arrière

- Fichiers : renvoyer la version précédente (`git checkout <tag> -- <chemin>`, puis envoi WinSCP).
- Base de données : restaurer la sauvegarde OVH, uniquement en dernier recours (les modifications du client faites depuis seront perdues).
- Dans tous les cas, prévenir le client si du contenu a pu être perdu.

## 8. Checklist rapide

- [ ] Quel cas (A, B, C, D) ?
- [ ] Sauvegarde faite (base et fichiers concernés).
- [ ] Testé en local, commité, poussé.
- [ ] Seuls les fichiers modifiés envoyés ; `.env` et `uploads/` intacts.
- [ ] Site en ligne vérifié (ordinateur, téléphone).
- [ ] Tag de déploiement posé.
- [ ] Client prévenu si pertinent.

## 9. Erreurs fréquentes

| Symptôme | Cause probable | Piste |
|---|---|---|
| La page créée en local n'existe pas en ligne | Le contenu est dans la base, pas dans les fichiers | Recréer la page en ligne (cas A) |
| Le pattern n'apparaît pas en ligne | Fichier non envoyé, mauvais dossier, en-tête de pattern invalide, ou version du thème inchangée (WordPress met en cache la liste des patterns par version) | Vérifier le chemin et l'en-tête ; incrémenter `Version` dans `style.css` et renvoyer le fichier |
| `/<slug>/` en 404 alors que la page existe | Règles de réécriture non écrites dans `.htaccess` | Réglages → Permaliens → Enregistrer |
| Page blanche après envoi | Erreur PHP (fichier partiel, `vendor/` manquant) | Renvoyer le fichier complet, vérifier `vendor/`, consulter les logs OVH |
| Images du client disparues | `uploads/` écrasé | Restaurer la sauvegarde ; ne plus envoyer `uploads/` |
| Site cassé avec « database connection » | `.env` écrasé | Recréer le `.env` (section 10 du guide nouveau client) |
| Changement de style invisible | Cache du navigateur ou de l'hébergeur | Rechargement forcé, vider le cache OVH |
| Réglages modifiés par le client écrasés | Le style personnalisé fait en admin (Éditeur du site) prime sur les fichiers | Éviter que le client modifie l'éditeur du site ; réinitialiser les personnalisations si nécessaire |

## 10. À améliorer plus tard

L'envoi manuel est la partie fragile. Quand SSH et une clé de déploiement seront en place, un workflow GitHub Actions pourra envoyer uniquement les fichiers modifiés après chaque tag, avec sauvegarde préalable. À décider après le premier client.

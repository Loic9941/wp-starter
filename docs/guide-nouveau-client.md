# Guide : créer un nouveau site client

Procédure à suivre pour chaque nouveau client, de zéro jusqu'à la mise en ligne. Elle s'appuie sur `wp-starter` et `wp-core`. Le contexte et les décisions sont dans `wizard-wp-starter.md`.

Dans ce document, `<client>` désigne le nom court du projet et `<domaine>` le domaine du client. Ne jamais écrire de mot de passe ni de token dans ce fichier ni dans un dépôt.

## 0. Prérequis (une seule fois)

- [ ] Docker Desktop installé et lancé, WSL2 actif.
- [ ] DDEV installé (`winget install -e --id DDEVFoundation.DDEV`).
- [ ] Git configuré (`user.name`, `user.email`).
- [ ] WinSCP installé.
- [ ] Un gestionnaire de mots de passe.
- [ ] Un token GitHub fine-grained, en lecture seule sur le contenu de `wp-core`. Composer le demandera au premier `ddev composer`.

## 1. Créer le repo du client

`wp-starter` doit être marqué « Template repository » (Settings du repo GitHub). Si ce n'est pas fait, le faire une fois.

1. Sur la page de `wp-starter`, cliquer sur « Use this template ».
2. Nom : `site-<client>`, visibilité **privée**.
3. Cloner le nouveau repo en local.

Le repo client est une copie : les évolutions futures de `wp-starter` n'y arrivent pas automatiquement. Seules celles de `wp-core` se propagent (voir la section 12).

## 2. Installer en local

Le `README.md` de `wp-starter` est la référence pour les commandes exactes. En résumé :

```powershell
cd <dossier du repo client>
copy .env.example .env
ddev start
ddev composer install
```

- Au premier `ddev composer`, coller le token GitHub quand il est demandé (rien ne s'affiche en collant, c'est normal).
- Si l'URL du site est mauvaise après l'installation, faire `ddev restart`.
- Installer WordPress en local (commandes `ddev wp core install ...` du README), puis activer le thème et `wp-core`.
- L'avertissement DDEV sur `wp-config.php` est sans gravité.

Vérification : `ddev launch` affiche le site avec le thème.

## 3. Personnaliser

- [ ] Couleurs, typographie, espacements dans `theme.json`.
- [ ] Contenu des patterns (hero, points forts, appel à l'action).
- [ ] Titre du site et langue (Réglages → Général).
- [ ] Contrôler les contrastes de texte (viser au moins 4,5 pour le texte normal).

Commiter régulièrement. Ne jamais commiter `.env`.

## 4. Fonctionnalités propres au client

- Plugins gratuits : les déclarer dans le `composer.json` du repo client (via WPackagist), et ajouter leur dossier au `.gitignore`.
- Plugins payants : installation manuelle, dossier ignoré par Git, noms et versions notés dans le README du client.
- Code propre à ce client : un petit plugin dans `wp-content/plugins/` du repo client, non ignoré.
- Ne rien mettre de spécifique à un client dans `wp-core` ni dans `wp-starter`.

Pour un site de réservation avec paiement, choisir entre un plugin de réservation avec add-on Mollie, et WooCommerce avec le plugin Mollie officiel. Tester le paiement en mode test avant la mise en ligne.

## 5. Choisir l'hébergement

Exigences minimales :

- [ ] Racine web configurable vers `web/` (critère décisif avec cette structure).
- [ ] PHP 8.x récent et MySQL/MariaDB.
- [ ] SFTP (SSH utile plus tard pour l'automatisation).
- [ ] Tâches planifiées (cron), indispensables pour la réservation.
- [ ] HTTPS gratuit.
- [ ] Sauvegardes, avec restauration testée.
- [ ] Un service d'envoi d'e-mails transactionnels (confirmations), plutôt que le mail de l'hébergeur.

Référence validée : OVHcloud mutualisé Pro. Commander sans module pré-installé, et avec l'engagement le plus court. Noter le prix de renouvellement et décider dès la commande si le renouvellement automatique reste actif.

## 6. Domaine et DNS (OVH)

Attention : le formulaire d'ajout de site peut compléter le nom avec le domaine et le doubler (`test.<domaine>.<domaine>`). Avant de valider, le domaine ne doit apparaître qu'une fois.

1. Hébergement → Mes sites → Ajouter un site, avec le domaine (ou un sous-domaine de test).
2. Si le domaine est géré chez OVH, accepter la configuration automatique du DNS.
3. Vérifier la zone DNS : les lignes A et AAAA doivent porter le nom exact.
4. Contrôler sans cache :
   ```powershell
   nslookup <nom> <serveur DNS d'OVH, par exemple dns109.ovh.net>
   ```
5. Attendre le certificat HTTPS (la colonne SSL passe à 1/1).

Si le nom est doublé : supprimer le site et les lignes DNS concernés, puis recommencer. Ne pas toucher aux autres lignes de la zone (e-mail, SPF, DKIM, autres sous-domaines).

## 7. Base de données

1. Hébergement → Bases de données → créer une base MySQL.
2. Relever le nom, l'utilisateur, le mot de passe et l'hôte (adresse complète du serveur, depuis la fiche de la base). Les enregistrer dans le gestionnaire de mots de passe.

## 8. Accès SFTP

- L'utilisateur SFTP a son propre mot de passe (onglet FTP-SSH, « Change the password »).
- Ne pas multiplier les essais ratés : plusieurs échecs peuvent bloquer l'adresse IP un moment. En cas de blocage, tester depuis un autre réseau.
- Se connecter avec WinSCP : protocole SFTP, port 22.

## 9. Construire et envoyer

```powershell
ddev composer install --no-dev --optimize-autoloader
```

Créer sur le serveur un dossier `<client>` à côté de `www`, puis envoyer avec WinSCP :

| Envoyer | Ne pas envoyer |
|---|---|
| `web/` | `.env` local |
| `vendor/` | `.ddev/`, `.git/` |
| `wp-config.php` (racine du projet) | `CLAUDE.md`, `README.md`, `composer.lock`, `wp-cli.yml` |
| | `web/wp-config-ddev.php` |
| | uploads locaux de test |

Après l'envoi, supprimer `web/wp-config-ddev.php` s'il est parti sur le serveur.

Pour les envois suivants (mises à jour) : ne jamais écraser `.env` ni le dossier des uploads du serveur.

Remettre ensuite l'environnement local en mode normal : `ddev composer install`.

## 10. Créer le `.env` du serveur

Sur le serveur, dans le dossier du projet (celui qui contient `vendor`, `web` et `wp-config.php`), créer `.env` avec WinSCP (Maj+F4). Contenu :

```
WP_ENV=production

DB_NAME=<nom de la base>
DB_USER=<utilisateur de la base>
DB_PASSWORD=<mot de passe de la base>
DB_HOST=<hôte complet donné par la fiche de la base>
DB_PREFIX=wp_

WP_HOME=https://<domaine>
```

Puis coller les huit clés et salts. Pour les générer, dans PowerShell sur le PC :

```powershell
$names = "AUTH_KEY","SECURE_AUTH_KEY","LOGGED_IN_KEY","NONCE_KEY","AUTH_SALT","SECURE_AUTH_SALT","LOGGED_IN_SALT","NONCE_SALT"
$chars = [char[]]'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
foreach ($n in $names) {
  $bytes = New-Object byte[] 64
  $rng.GetBytes($bytes)
  $v = -join ($bytes | ForEach-Object { $chars[$_ % $chars.Length] })
  "$n='$v'"
}
```

Si le certificat HTTPS n'est pas encore actif, mettre `WP_HOME` en `http://` temporairement, puis le passer en `https://` ensuite (et revenir à `http://` en cas de boucle de redirection).

Si WordPress affiche « Error establishing a database connection », la valeur de `DB_HOST` est la première chose à vérifier.

## 11. Pointer la racine web, installer, sécuriser

1. Hébergement → Mes sites → modifier le site → dossier racine `./<client>/web` (pas `./<client>`).
2. Ouvrir le site : l'écran d'installation de WordPress doit apparaître.
3. Terminer l'installation immédiatement (sinon n'importe qui peut la faire) :
   - langue : français ;
   - identifiant administrateur non devinable (pas `admin`) ;
   - mot de passe long et unique ;
   - cocher « Demander aux moteurs de recherche de ne pas indexer ce site » tant que le site n'est pas livré.
4. Se connecter (l'administration est probablement sous `/wp/wp-admin/`, à confirmer sur chaque site) et activer le thème et `wp-core`.
5. Si le mot de passe a été saisi avant le HTTPS, le changer.

Vérifications de sécurité :
- [ ] `https://<domaine>/.env` et `https://<domaine>/vendor/` ne répondent pas (erreur attendue).
- [ ] Le cadenas HTTPS est valide, sans avertissement.

## 12. Mettre à jour `wp-core`

1. Développer dans `wp-core` (ou utiliser `dev-main` côté projet pour tester sans tag).
2. Pousser et taguer : `git tag vX.Y.Z` puis `git push --tags`.
3. Dans chaque client concerné : `ddev composer update loic9941/wp-core`, commit du `composer.lock`, reconstruction et renvoi des fichiers.

Tant que `wp-core` est en version `0.x`, une contrainte `^0.1` n'accepte pas `0.2.0` : élargir la contrainte ou passer en `1.0.0` quand l'API se stabilise. Tester sur un site de test avant la production.

## 13. Checklist de livraison

- [ ] Compte Éditeur créé pour le client, testé : modifier un texte, ajouter une section depuis les patterns, remplacer une image.
- [ ] Le client ne voit pas ce qu'il ne doit pas toucher (réglages, éditeur du site).
- [ ] Sauvegardes actives, avec une restauration testée.
- [ ] E-mails de confirmation testés (service transactionnel).
- [ ] Paiement testé en mode test, puis en mode réel avec un petit montant.
- [ ] Case « ne pas indexer » décochée au moment de la mise en ligne.
- [ ] Domaine définitif branché, HTTPS valide, `WP_HOME` à jour.
- [ ] Mot de passe administrateur transmis par un canal sûr, puis changé par le client.
- [ ] Renouvellement de l'hébergement : qui paie, et qui reçoit les rappels.
- [ ] README du repo client à jour (plugins payants, versions, particularités).

## 14. Erreurs fréquentes

| Symptôme | Cause probable | Piste |
|---|---|---|
| `NXDOMAIN` | Nom doublé dans la zone DNS ou le site OVH | Section 6 |
| Avertissement de certificat | Certificat généré pour un autre nom ou pas encore prêt | Attendre, ou recréer le site avec le bon nom |
| « Index of / » | Dossier racine pas encore pointé sur `web/` | Section 11 |
| « Error establishing a database connection » | `DB_HOST` ou identifiants du `.env` | Section 10 |
| `Permission denied` puis `timed out` en SFTP | Trop d'essais, adresse bloquée | Section 8 |
| Page blanche ou erreur PHP | Version de PHP, extension manquante, `vendor/` absent | Vérifier la version de PHP de l'hébergement et que `vendor/` a bien été envoyé |
| Boucle de redirections après `https://` | `WP_HOME` et certificat désalignés | Remettre `http://`, corriger le certificat, réessayer |

# Installer Moncine sur YunoHost (tests)

## Prérequis

- Serveur **YunoHost 11.2+** (idéalement 12.x)
- PHP **8.4** (ou 8.3) avec extensions sqlite3, mbstring, intl, curl — voir `manifest.toml` si besoin d’adapter la version
- Accès **admin** au serveur (SSH ou console)

## Structure du paquet

Le paquet est à la **racine du dépôt** (convention YunoHost v2) :

```text
Moncine/
├── manifest.toml
├── scripts/          install, upgrade, backup, restore, remove
├── conf/             nginx.conf, extra_php-fpm.conf
├── www/              pages web
├── lib/
├── sql/
└── data/             (local uniquement, non installé sur le serveur)
```

## Installation depuis ce dépôt (test)

1. Copiez ou clonez le projet sur le serveur YunoHost, par exemple :

   ```bash
   scp -r Moncine admin@votre-serveur:/tmp/Moncine
   ```

2. Sur le serveur, en root ou avec `sudo` :

   ```bash
   sudo yunohost app install /tmp/Moncine \
     -a moncine.votredomaine.tld \
     -p /
   ```

   - `-a` : sous-domaine dédié (recommandé)
   - `-p /` : chemin web à la racine du domaine (les liens de l’app utilisent des chemins absolus `/…`)

3. Ouvrez `https://moncine.votredomaine.tld/` et créez le **premier compte administrateur**.

## Commandes utiles

```bash
# Mise à jour après modification du code
sudo yunohost app upgrade moncine -u /chemin/vers/Moncine

# Sauvegarde
sudo yunohost app backup moncine

# Liste des sauvegardes
sudo yunohost app backup list moncine

# Désinstallation (données conservées sauf --purge)
sudo yunohost app remove moncine
sudo yunohost app remove moncine --purge
```

## Où sont les données ?

| Élément | Emplacement typique |
|---------|---------------------|
| Code | `/var/www/moncine` |
| Base SQLite, clé TMDB | `/home/yunohost.app/moncine/` (`MONCINE_DATA_PATH`) |
| Affiches téléchargées | `/var/www/moncine/www/posters/` |

## E-mails (mot de passe oublié)

Configurez l’envoi de mail sur YunoHost (postfix). Optionnel :

- `MONCINE_MAIL_FROM` dans `extra_php-fpm.conf` ou panneau avancé
- Voir aussi [comptes-mot-de-passe.md](comptes-mot-de-passe.md)

## Dépannage

| Problème | Piste |
|----------|--------|
| Page blanche / 502 | `sudo yunohost app log moncine` ; vérifier PHP-FPM |
| Erreur migration | `sudo -u moncine php /var/www/moncine/lib/cli/migrate.php` avec `MONCINE_DATA_PATH` |
| Liens cassés | Réinstaller avec `-p /` sur un sous-domaine dédié |

## Publication apps.yunohost.org

Non couvert ici : il faudra une archive release avec `url` + `sha256` dans `manifest.toml`, tests CI, et dépôt `YunoHost-Apps/moncine_ynh`.

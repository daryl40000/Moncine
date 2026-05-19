# Moncine

**Version : 0.5.0** — environ **50 %** de la vision fonctionnelle cible (voir [ROADMAP.md](ROADMAP.md)).

**Auteur :** Stéphane MATER  
**Licence :** [GNU General Public License v3.0 ou ultérieure](LICENSE) (GPL-3.0-or-later)

Application web pour gérer une **dvdthèque personnelle** : films, envies, notes, enrichissement TMDB, import/export CSV, comptes utilisateurs.

Le **code source** (upstream) vit dans ce dépôt. Le **paquet YunoHost** est maintenu à part, dans le dépôt voisin **Moncine-yunohost**, synchronisé à partir de celui-ci.

---

## Fonctionnalités actuelles (v0.5)

| Domaine | Disponible |
|---------|------------|
| Collection & envies | Mes films, Mes envies, sagas, statistiques, quiz |
| Catalogue partagé | Fiches œuvres, enrichissement TMDB / OMDB, affiches |
| Comptes | Connexion, rôles admin/utilisateur, gestion des comptes |
| Mots de passe | Mon compte, changement, oublié par e-mail, reset admin |
| Exemplaire personnel | Support, format image/son (séparés du catalogue) |
| Données | Import / export CSV, affiches |

### Prochaines étapes (v0.5 → v1.0)

- Admin catalogue (doublons, journal)
- Foyers & famille (collection partagée, envies personnelles)
- Soumissions au catalogue
- Mes BD

Détail : [ROADMAP.md](ROADMAP.md).

---

## Comprendre le code (par où commencer)

| Fichier | Rôle |
|---------|------|
| `lib/bootstrap.php` | Chargé par chaque page : config, base, connexion obligatoire |
| `lib/Auth.php` | Qui est connecté, login, pages publiques |
| `lib/UserContext.php` | ID utilisateur pour « Mes films » / envies |
| `lib/Database.php` | SQLite + migrations automatiques |
| `lib/FilmRepository.php` | Accès aux films de l’utilisateur courant |
| `www/*.php` | Une page = un fichier (contrôleur léger) |
| `templates/*.php` | HTML affiché (via `View::render`) |

---

## Structure du projet

```text
Moncine/
├── www/              pages web
├── lib/              code PHP (+ cli/migrate.php)
├── templates/        vues HTML
├── sql/
│   ├── schema.sql    schéma complet (install fraîche)
│   ├── migrations/   évolutions SQL (001, 002…)
│   └── migrations_legacy/  historique dev (non exécuté)
├── data/             base SQLite, clés API, affiches (non versionné)
├── tests/            tests PHPUnit
└── doc/
```

---

## Prérequis

- PHP **8.2+** avec extension **sqlite3**
- [Composer](https://getcomposer.org/) (pour les tests)

---

## Installation et test en local

```bash
cd /chemin/vers/Moncine
composer install
php lib/cli/migrate.php --fresh   # première fois (crée data/moncine.db)
php -S localhost:8080 -t www
```

Ouvrir http://localhost:8080 — à la première visite, créez le **compte administrateur** sur `/premier-compte.php`.

### Variables d’environnement utiles

| Variable | Rôle |
|----------|------|
| `MONCINE_DATA_PATH` | Dossier des données (base SQLite, clés API, affiches). Par défaut : `./data/` |
| `MONCINE_BASE_URL` | URL publique de l’app (liens dans les e-mails de réinitialisation de mot de passe) |

---

## Comptes utilisateurs

- **Premier lancement** : `/premier-compte.php` (administrateur)
- **Connexion** : `/connexion.php`
- **Mon compte** : `/mon-compte.php`
- **Gestion des comptes** : `/utilisateurs.php` (admin uniquement)

Chaque utilisateur a sa propre bibliothèque et ses envies.

Documentation mots de passe : [doc/comptes-mot-de-passe.md](doc/comptes-mot-de-passe.md).

---

## Migrations SQL

- **Install fraîche** : `sql/schema.sql` si la base est vide, puis `sql/migrations/*.sql`
- **Mise à jour** : `php lib/cli/migrate.php`

Les fichiers dans `sql/migrations_legacy/` ne sont **pas** appliqués (historique uniquement).

---

## Tests automatisés (PHPUnit)

Vérifie l’import/export (détection de format, parsing CSV, import bibliothèque et catalogue) sur une base SQLite temporaire.

```bash
composer test
```

---

## Import / export

- **Export** : page `/export.php` (CSV collection, envies, historique)
- **Import** : page `/import.php` (bibliothèque ou catalogue admin)

Les affiches locales sont stockées dans `data/posters/` (ou le dossier défini par `MONCINE_DATA_PATH`).

---

## Déploiement YunoHost

Le packaging YunoHost (scripts d’install, nginx, manifest) se trouve dans le dépôt **Moncine-yunohost**, mis à jour à partir de ce dépôt upstream.

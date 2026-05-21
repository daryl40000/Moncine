# Journal des versions (Moncine)

Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/).
Les numéros suivent le [versionnement sémantique](https://semver.org/lang/fr/).

**Tags Git :** à partir de **v0.7.9**, les releases sont taguées `v0.7.x`. Le tag historique `0.7` couvrait plusieurs versions intermédiaires non taguées individuellement.

---

## [0.7.9] — 2026-05-21

### Amélioré

- Lisibilité des liens sur thème sombre (couleurs dédiées, compatibilité `color-scheme: dark`).
- Onglets **Mes films** (Tout, Film, Série…) et **Liste / Vignettes** : contraste stable (texte foncé sur fond doré), y compris pour les liens déjà visités (`:visited`).
- Filtres **Support physique** et onglets **Mes envies / Envies du groupe** : même composant visuel.

### Technique (dette priorité haute)

- Composant CSS réutilisable **`.ui-pill`** / **`.ui-pill-bar`** pour les filtres et onglets (remplace les anciennes classes par page).
- Règles de liens de contenu simplifiées (sélecteurs ciblés `.lead`, `.hint`, etc. — plus de longue chaîne `:not()` sur `main`).
- Fichier **`CHANGELOG.md`** ajouté pour documenter les releases.

### Déploiement

- Aucune migration SQL.
- Remplacer les fichiers ; vider le cache navigateur si besoin (Ctrl+F5).

---

## [0.7.8] — 2026-05-19

### Ajouté

- **Envies du groupe** : agrégation par œuvre, tri par nombre de demandes, liste des votants, bouton « Moi aussi ».
- Ajout en un clic dans la bibliothèque après proposition catalogue acceptée.
- Notifications enrichies (proposition acceptée → lien vers ajout rapide).

---

## [0.7.7] — 2026-05-19

### Ajouté

- **Amis** : demandes, acceptation, refus.
- **Groupes famille** créés par les utilisateurs (remplace la création de foyers par l’admin).
- Invitations au groupe ; `/foyers.php` admin en lecture seule.

### Changé

- Nouveaux comptes sans foyer assigné automatiquement.

---

## [0.7.6] — 2026-05-19

### Ajouté

- Ville optionnelle sur le profil.
- Recherche d’utilisateurs par pseudo / ville.
- Opt-out « masquer mon profil de la recherche ».
- Cloche de notifications compacte dans l’en-tête.

---

## [0.7.4] — 2026-05-19

### Ajouté

- Soumissions au catalogue (proposer, valider, refuser).
- Notifications in-app et par e-mail.
- Navigation Préc./Suiv. entre fiches catalogue et films.

---

## [0.7.0] — 2026-05-19

### Ajouté

- Foyers et collection partagée.
- Envies et historique personnels par utilisateur.

---

## Liens

- Détail des phases futures : [ROADMAP.md](ROADMAP.md)
- Installation et usage : [README.md](README.md)

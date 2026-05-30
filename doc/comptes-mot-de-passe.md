# Comptes et mots de passe — Moncine

## Pour les utilisateurs

- **Inscription publique** (si activée par l’admin) : voir [inscription-utilisateurs.md](inscription-utilisateurs.md).
- **Mon compte** (`/parametres.php`) : modifier le nom, l’e-mail et le mot de passe.
- **Supprimer mon compte** (même page, section dédiée) : réservé aux comptes **utilisateur** (pas aux administrateurs). Il faut saisir le **mot de passe actuel** et confirmer. Après suppression, redirection vers la page de connexion.
- **Mot de passe oublié** (`/mot-de-passe-oublie.php`) : recevoir un lien par e-mail (valable 1 heure).
- Un administrateur peut aussi vous donner un **mot de passe provisoire** ; changez-le ensuite dans Mon compte.

### Suppression du compte (détail)

| Élément | Comportement |
|---------|----------------|
| **Qui peut supprimer ?** | Tout utilisateur connecté **sauf** les comptes avec le rôle administrateur. |
| **Données supprimées** | Compte, envies personnelles, historique de vision personnel. |
| **Groupe famille** | Les films déjà ajoutés à la **collection partagée** restent pour les autres membres ; la fiche est réattribuée à un autre membre du groupe (fondateur en priorité). |
| **Demandes d’inscription** | Les demandes liées à votre e-mail sont effacées. |
| **Administrateur** | Un admin supprime les comptes depuis **Comptes utilisateurs** (`/utilisateurs.php`), pas depuis Mon compte. |

## Pour l’administrateur (YunoHost)

### Envoi des e-mails

La réinitialisation utilise la fonction PHP `mail()`. Sur YunoHost, configurez l’envoi de mails du serveur (ex. `postfix`) ou définissez :

| Variable | Rôle |
|----------|------|
| `MONCINE_MAIL_FROM` | Adresse expéditeur (ex. `moncine@votredomaine.fr`) |
| `MONCINE_BASE_URL` | URL publique de l’app (ex. `https://moncine.example.net`) si le lien dans l’e-mail est incorrect |

Sans serveur mail fonctionnel, les utilisateurs peuvent demander à l’admin un **Réinit. MDP** depuis la page Comptes.

### Migration base

Après mise à jour du paquet :

```bash
php lib/cli/migrate.php
```

Cela applique les migrations en attente (ex. `004` mots de passe, `027`–`028` inscription si vous activez l’inscription publique).

### Sécurité

- Limite de tentatives sur la connexion, l’**inscription** et « mot de passe oublié » (session + compteurs **par IP** dans `data/auth_rate_limit/`, non contournables en changeant de navigateur).
- Jetons stockés **hachés** en base ; usage unique.
- Message neutre si l’e-mail n’existe pas (pas d’énumération des comptes).

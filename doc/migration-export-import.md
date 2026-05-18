# Migration des données (ancienne prod → paquet YunoHost)

## Principe

1. **Exporter** depuis `Moncine (origine)` (CSV + dossier `www/posters/` si affiches locales).
2. **Installer** le paquet Moncine (base vide, schéma paquet).
3. **Importer** via la page Importer (format à étendre pour envies + historique).
4. Vérifier les comptages, puis basculer l’URL.

Pas de copie directe de `moncine.db` : le schéma évoluera (comptes, foyers, champs déplacés).

## Checklist

- [ ] Export collection (Mes films)
- [ ] Export wishlist (Mes envies) — colonne statut `wishlist` / `mes envies`
- [ ] Export historique (visions + notes)
- [ ] Sauvegarde `data/` et `www/posters/`
- [ ] Import sur paquet neuf
- [ ] Test sur URL de préproduction

## À développer (roadmap)

- Import unifié « migration legacy » reconnaissant l’export de l’ancienne app
- Rapport post-import (films importés, lignes ignorées, doublons)

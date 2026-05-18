<?php
/**
 * Formulaire de correction manuelle d’une fiche film.
 *
 * @var array<string, mixed> $film
 * @var int $filmId
 * @var bool $editOpen
 * @var string $saveError
 */
$editOpen = $editOpen ?? false;
$saveError = $saveError ?? '';
?>
<details class="film-edit-panel"<?= $editOpen ? ' open' : '' ?>>
    <summary class="film-edit-panel__summary">Modifier manuellement la fiche</summary>

    <p class="hint">
        Corrigez vous-même le titre, le synopsis, l’affiche, etc. Les champs laissés vides
        (année, durée) peuvent être effacés en vidant le champ.
    </p>

    <?php if ($saveError !== ''): ?>
        <div class="alert alert-warning"><?= Moncine\View::escape($saveError) ?></div>
    <?php endif; ?>

    <form method="post" action="/modifier-film.php" class="film-edit-form">
        <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
        <input type="hidden" name="film_id" value="<?= (int) $filmId ?>">

        <fieldset>
            <legend>Informations principales</legend>

            <label for="edit_titre">Titre <span class="required">*</span></label>
            <input type="text" name="titre" id="edit_titre" required
                   value="<?= Moncine\View::escape((string) $film['titre']) ?>">

            <label for="edit_titre_original">Titre original</label>
            <input type="text" name="titre_original" id="edit_titre_original"
                   placeholder="ex. The Godfather (rempli par TMDB si différent du titre français)"
                   value="<?= Moncine\View::escape((string) ($film['titre_original'] ?? '')) ?>">

            <label for="edit_realisateur">Réalisateur</label>
            <input type="text" name="realisateur" id="edit_realisateur"
                   value="<?= Moncine\View::escape((string) ($film['realisateur'] ?? '')) ?>">

            <label for="edit_acteur_1">Acteur principal 1</label>
            <input type="text" name="acteur_1" id="edit_acteur_1"
                   value="<?= Moncine\View::escape((string) ($film['acteur_1'] ?? '')) ?>">

            <label for="edit_acteur_2">Acteur principal 2</label>
            <input type="text" name="acteur_2" id="edit_acteur_2"
                   value="<?= Moncine\View::escape((string) ($film['acteur_2'] ?? '')) ?>">

            <label for="edit_acteur_3">Acteur principal 3</label>
            <input type="text" name="acteur_3" id="edit_acteur_3"
                   value="<?= Moncine\View::escape((string) ($film['acteur_3'] ?? '')) ?>">

            <label for="edit_annee">Année</label>
            <input type="text" name="annee" id="edit_annee" inputmode="numeric" pattern="[0-9]{4}"
                   placeholder="1982"
                   value="<?= (int) ($film['annee'] ?? 0) > 0 ? (int) $film['annee'] : '' ?>">

            <label for="edit_nationalite">Nationalité / pays</label>
            <input type="text" name="nationalite" id="edit_nationalite"
                   placeholder="ex. France, États-Unis (rempli par TMDB)"
                   value="<?= Moncine\View::escape((string) ($film['nationalite'] ?? '')) ?>">

            <label for="edit_duree">Durée</label>
            <input type="text" name="duree" id="edit_duree"
                   placeholder="1h56 ou 116"
                   value="<?= Moncine\View::escape(Moncine\FilmManualEdit::dureeForInput((int) ($film['duree_min'] ?? 0))) ?>">

            <label for="edit_styles">Style(s)</label>
            <input type="text" name="styles" id="edit_styles"
                   placeholder="Action, Science-fiction"
                   value="<?= Moncine\View::escape((string) ($film['styles'] ?? '')) ?>">

            <label for="edit_saga">Saga</label>
            <input type="text" name="saga" id="edit_saga" list="edit_saga_list"
                   placeholder="ex. Jason Bourne (laisser vide pour retirer)"
                   value="<?= Moncine\View::escape((string) ($film['saga'] ?? '')) ?>">
            <?php if (!empty($sagaSuggestions)): ?>
                <datalist id="edit_saga_list">
                    <?php foreach ($sagaSuggestions as $sagaHint): ?>
                        <option value="<?= Moncine\View::escape($sagaHint) ?>">
                    <?php endforeach; ?>
                </datalist>
            <?php endif; ?>

            <label for="edit_saga_ordre">N° dans la saga</label>
            <input type="number" name="saga_ordre" id="edit_saga_ordre" min="1" max="999" step="1"
                   placeholder="1, 2, 3…"
                   value="<?= (int) ($film['saga_ordre'] ?? 0) > 0 ? (int) $film['saga_ordre'] : '' ?>">
        </fieldset>

        <fieldset>
            <legend>Catégorie</legend>
            <p class="hint">
                Indiquez s’il s’agit d’un film, d’une série, d’un documentaire ou d’un spectacle.
                Cela s’affiche dans la colonne « Type » de Mes films.
            </p>
            <?php
            $fieldPrefix = 'edit';
            require MONCINE_ROOT . '/templates/_film_content_kind_fields.php';
            ?>
        </fieldset>

        <fieldset>
            <legend>Support &amp; texte</legend>

            <label for="edit_format_image">Format image</label>
            <input type="text" name="format_image" id="edit_format_image"
                   placeholder="Blu-ray, DVD…"
                   value="<?= Moncine\View::escape((string) ($film['format_image'] ?? '')) ?>">

            <label for="edit_format_son">Bande sonore</label>
            <input type="text" name="format_son" id="edit_format_son"
                   placeholder="VF, VOST…"
                   value="<?= Moncine\View::escape((string) ($film['format_son'] ?? '')) ?>">

            <label for="edit_support_physique">Support physique</label>
            <select name="support_physique" id="edit_support_physique">
                <option value="">— Non renseigné —</option>
                <?php
                $currentSupport = (string) ($film['support_physique'] ?? '');
                foreach (Moncine\SupportPhysique::choices() as $key => $label):
                    $sel = $currentSupport === $key ? ' selected' : '';
                    ?>
                    <option value="<?= Moncine\View::escape($key) ?>"<?= $sel ?>><?= Moncine\View::escape($label) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="edit_poster_url">Affiche (URL ou chemin local)</label>
            <input type="text" name="poster_url" id="edit_poster_url"
                   placeholder="https://… ou /posters/123.jpg"
                   value="<?= Moncine\View::escape((string) ($film['poster_url'] ?? '')) ?>">
            <p class="hint">Une URL HTTPS sera copiée automatiquement dans <code>/posters/</code> à l’enregistrement.</p>

            <label for="edit_synopsis">Synopsis</label>
            <textarea name="synopsis" id="edit_synopsis" rows="6"
                      placeholder="Résumé du film…"><?= Moncine\View::escape((string) ($film['synopsis'] ?? '')) ?></textarea>
        </fieldset>

        <fieldset>
            <legend>TMDB (optionnel)</legend>

            <label for="edit_tmdb">Identifiant TMDB</label>
            <input type="text" name="tmdb_id" id="edit_tmdb"
                   placeholder="78, /movie/78 ou /tv/1396"
                   value="<?= (int) ($film['tmdb_id'] ?? 0) > 0 ? (int) $film['tmdb_id'] : '' ?>">
        </fieldset>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
</details>

<section>
    <h1>Importer ma dvdthèque</h1>
    <p class="lead">
        Réimportez un export Moncine (<strong>CSV</strong> point-virgule ou <strong>ODS</strong>) :
        toutes les colonnes de la fiche (titre original, support DVD/Blu-ray, <strong>saga</strong>,
        <strong>nationalité</strong>, <strong>statut</strong> (mes films ou mes envies), type TMDB, acteurs, synopsis…)
        et l’historique des visions (feuille <strong>Historique</strong> dans l’ODS, ou colonnes
        <strong>Vu</strong> / <strong>Note</strong> en CSV).
        Un ancien fichier sans certaines colonnes ne les efface pas à la réimportation.
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($enrichMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($enrichMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-warning">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= Moncine\View::escape($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="import-form">
        <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
        <label for="csv_file">Fichier CSV ou ODS (export Moncine)</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv,.ods,text/csv" required>

        <label class="checkbox">
            <input type="checkbox" name="replace_all" value="1">
            Remplacer tous mes films (supprime les films existants avant import)
        </label>
        <p class="hint">Taille maximale du fichier : <?= (int) (MONCINE_CSV_MAX_BYTES / 1024 / 1024) ?> Mo.</p>

        <button type="submit" class="btn btn-primary">Importer</button>
    </form>

    <p class="hint">
        <a href="/samples/films-exemple.csv">Télécharger un fichier d'exemple</a>
    </p>
</section>

<section class="export-panel">
    <h2>Exporter mes films</h2>
    <p class="lead">
        Téléchargez l’ensemble de votre base : tous les films et leurs informations
        (année, saga, nationalité, support, acteurs, synopsis TMDB…). Le fichier CSV peut être réouvert dans Excel ;
        le fichier ODS contient en plus une feuille <strong>Historique</strong> avec toutes les dates de vision.
    </p>

    <?php if ((int) $filmCount === 0): ?>
        <p class="hint">Aucun film en base — importez d’abord un fichier CSV.</p>
    <?php else: ?>
        <p class="stats"><?= (int) $filmCount ?> film<?= $filmCount > 1 ? 's' : '' ?> seront exporté<?= $filmCount > 1 ? 's' : '' ?>.</p>
        <div class="export-actions">
            <form method="post" action="/export.php" class="inline-form">
                <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                <input type="hidden" name="format" value="csv">
                <button type="submit" class="btn btn-secondary">Télécharger en CSV</button>
            </form>
            <form method="post" action="/export.php" class="inline-form">
                <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                <input type="hidden" name="format" value="ods">
                <button type="submit" class="btn btn-secondary">Télécharger en ODS</button>
            </form>
        </div>
        <p class="hint">
            CSV : point-virgule (<code>;</code>), une feuille avec toutes les colonnes de l’export.
            ODS : feuilles <strong>Films</strong> et <strong>Historique</strong> réimportées entièrement.
        </p>
        <details class="import-columns-help">
            <summary>Liste des colonnes exportées / importées</summary>
            <p class="hint"><?= Moncine\View::escape(Moncine\CollectionExportSchema::filmColumnLabelsText()) ?></p>
        </details>
    <?php endif; ?>
</section>

<section class="enrich-panel">
    <h2>Enrichir mes films (TMDB)</h2>
    <p class="lead">
        <strong>TMDB</strong> complète vos fiches : films, séries, <strong>documentaires</strong>
        et <strong>émissions TV</strong> — synopsis <strong>en français</strong>, affiche, année,
        durée, créateur/réalisateur et acteurs principaux.
    </p>

    <?php if (!empty($tmdbMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($tmdbMessage) ?></div>
    <?php endif; ?>

    <p class="hint">
        Créez un compte sur
        <a href="https://www.themoviedb.org/signup" target="_blank" rel="noopener">themoviedb.org</a>,
        puis demandez une clé « API » (type Developer) sur
        <a href="https://www.themoviedb.org/settings/api" target="_blank" rel="noopener">Paramètres → API</a>.
        Copiez la clé <strong>API Key (v3 auth)</strong>.
    </p>

    <?php if (!$hasTmdbKey): ?>
        <form method="post" action="/enrichir.php" class="import-form">
            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
            <input type="hidden" name="action" value="save_tmdb_key">
            <label for="tmdb_api_key">Clé API TMDB</label>
            <input type="password" name="tmdb_api_key" id="tmdb_api_key" required autocomplete="off"
                   placeholder="ex. a1b2c3d4e5f6…">
            <button type="submit" class="btn btn-secondary">Enregistrer la clé TMDB</button>
        </form>
    <?php else: ?>
        <p class="hint">✓ Clé TMDB configurée.</p>
        <form method="post" action="/enrichir.php" class="inline-form">
            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
            <input type="hidden" name="action" value="test_tmdb">
            <button type="submit" class="btn btn-secondary btn-sm">Tester la connexion TMDB</button>
        </form>
        <form method="post" action="/enrichir.php" class="inline-form enrich-key-update">
            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
            <input type="hidden" name="action" value="save_tmdb_key">
            <label for="tmdb_api_key_new">Changer la clé TMDB</label>
            <input type="password" name="tmdb_api_key" id="tmdb_api_key_new" autocomplete="off" placeholder="Nouvelle clé">
            <button type="submit" class="btn btn-ghost btn-sm">Mettre à jour</button>
        </form>

        <p class="stats">
            <?= (int) $filmCount ?> film(s) en base —
            <strong><?= (int) $enrichPending ?></strong> encore à enrichir.
        </p>
        <form method="post" action="/enrichir.php" class="import-form enrich-actions">
            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
            <input type="hidden" name="action" value="enrichir">
            <p class="hint">
                Environ <?= (int) $enrichBatchSize ?> films par clic (synopsis en français).
                <?php if ($enrichPending > 0): ?> Répétez jusqu’à « terminé ».<?php endif; ?>
            </p>
            <button type="submit" class="btn btn-accent">Enrichir la base</button>
            <label class="checkbox">
                <input type="checkbox" name="force_all" value="1">
                Tout retraiter (même les films déjà cherchés)
            </label>
        </form>
    <?php endif; ?>
</section>

<section class="export-panel">
    <h2>Affiches locales</h2>
    <p class="lead">
        Copiez les affiches sur votre serveur (<code>www/posters/</code>) pour ne plus dépendre d’Internet
        pour les afficher. Les enrichissements TMDB le font désormais automatiquement.
    </p>
    <p>
        <a href="/ranger-affiches.php" class="btn btn-secondary">Gérer les affiches locales</a>
    </p>
</section>

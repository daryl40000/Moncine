<?php
/**
 * @var string $activeTab library|posters|admin
 * @var bool $canManageCatalog
 */
$activeTab = in_array($activeTab ?? 'library', ['library', 'posters', 'admin'], true)
    ? $activeTab
    : 'library';
if (!$canManageCatalog && $activeTab === 'admin') {
    $activeTab = 'library';
}

$pillClass = static function (string $tab) use ($activeTab): string {
    return 'ui-pill' . ($activeTab === $tab ? ' ui-pill--active' : '');
};

$panelClass = static function (string $tab) use ($activeTab): string {
    return 'import-panel' . ($activeTab === $tab ? ' import-panel--active' : '');
};
?>
<section class="import-page">
    <h1>Importer / exporter</h1>
    <p class="lead">
        Gérez votre <strong>bibliothèque</strong> (films possédés, envies, notes)
        <?php if ($canManageCatalog): ?>
            et, en tant qu’administrateur, le <strong>catalogue partagé</strong> du site.
        <?php else: ?>
            . Les métadonnées communes (synopsis, affiches) sont gérées par l’administrateur.
        <?php endif; ?>
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($posterZipMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($posterZipMessage) ?></div>
    <?php endif; ?>
    <?php if (!empty($posterRemapMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($posterRemapMessage) ?></div>
    <?php endif; ?>
    <?php if (!empty($enrichMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($enrichMessage) ?></div>
    <?php endif; ?>
    <?php if (!empty($tmdbMessage)): ?>
        <div class="alert alert-success"><?= Moncine\View::escape($tmdbMessage) ?></div>
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

    <nav class="ui-pill-nav import-page__tabs" aria-label="Sections import et export">
        <a href="/import.php?tab=library" class="<?= $pillClass('library') ?>"
           <?= $activeTab === 'library' ? ' aria-current="page"' : '' ?>>Ma bibliothèque</a>
        <a href="/import.php?tab=posters" class="<?= $pillClass('posters') ?>"
           <?= $activeTab === 'posters' ? ' aria-current="page"' : '' ?>>Affiches</a>
        <?php if ($canManageCatalog): ?>
            <a href="/import.php?tab=admin" class="<?= $pillClass('admin') ?> site-nav__admin"
               <?= $activeTab === 'admin' ? ' aria-current="page"' : '' ?>>Administration</a>
        <?php endif; ?>
    </nav>

    <div class="import-page__panels">
        <section class="<?= $panelClass('library') ?>" id="import-panel-library"
                 <?= $activeTab !== 'library' ? ' hidden' : '' ?>>
            <h2 class="import-panel__title">Ma bibliothèque</h2>
            <p class="hint">
                Exportez depuis Excel ou LibreOffice, modifiez votre liste, puis réimportez le fichier ici.
            </p>

            <ol class="import-steps">
                <li><a href="#import-library-export">Exporter</a> ma liste actuelle (CSV ou ODS)</li>
                <li>Modifier le fichier sur mon ordinateur</li>
                <li><a href="#import-library-upload">Importer</a> le fichier mis à jour</li>
            </ol>

            <div class="import-panel__block" id="import-library-upload">
                <h3>Importer un fichier</h3>
                <form method="post" enctype="multipart/form-data" class="import-form">
                    <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                    <input type="hidden" name="import_scope" value="library">
                    <label for="csv_file">Fichier CSV ou ODS</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv,.ods,text/csv" required>

                    <label class="checkbox">
                        <input type="checkbox" name="replace_all" value="1">
                        Remplacer toute ma bibliothèque avant import (films, envies et historique)
                    </label>
                    <p class="hint">
                        Format détecté automatiquement. Taille max.
                        <?= (int) (MONCINE_CSV_MAX_BYTES / 1024 / 1024) ?> Mo.
                    </p>

                    <button type="submit" class="btn btn-primary">Importer ma bibliothèque</button>
                </form>
            </div>

            <div class="import-panel__block" id="import-library-export">
                <h3>Exporter ma bibliothèque</h3>
                <p class="hint">
                    Films possédés et envies : support, saga, notes, dernières visions…
                    (sans synopsis ni affiche).
                </p>
                <?php if ((int) ($libraryCount ?? 0) === 0): ?>
                    <p class="hint">Aucune entrée en bibliothèque pour l’instant.</p>
                <?php else: ?>
                    <p class="stats"><?= (int) $libraryCount ?> entrée(s) (films + envies).</p>
                    <div class="export-actions">
                        <form method="post" action="/export.php" class="inline-form">
                            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                            <input type="hidden" name="scope" value="library">
                            <input type="hidden" name="format" value="csv">
                            <input type="hidden" name="return" value="/import.php?tab=library">
                            <button type="submit" class="btn btn-secondary">CSV</button>
                        </form>
                        <form method="post" action="/export.php" class="inline-form">
                            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                            <input type="hidden" name="scope" value="library">
                            <input type="hidden" name="format" value="ods">
                            <input type="hidden" name="return" value="/import.php?tab=library">
                            <button type="submit" class="btn btn-secondary">ODS</button>
                        </form>
                    </div>
                    <details class="import-columns-help">
                        <summary>Colonnes du fichier exporté</summary>
                        <p class="hint"><?= Moncine\View::escape(Moncine\LibraryExportSchema::columnLabelsText()) ?></p>
                    </details>
                <?php endif; ?>
            </div>
        </section>

        <section class="<?= $panelClass('posters') ?>" id="import-panel-posters"
                 <?= $activeTab !== 'posters' ? ' hidden' : '' ?>>
            <h2 class="import-panel__title">Affiches</h2>
            <p class="hint">
                Les images sont liées au <strong>numéro du catalogue</strong>
                (<code>123.jpg</code> = œuvre n°123). Importez d’abord le catalogue CSV, puis les affiches.
            </p>

            <p>
                <a href="/ranger-affiches.php" class="btn btn-secondary">Télécharger les affiches depuis TMDB</a>
            </p>

            <?php if ($canManageCatalog): ?>
                <div class="import-panel__block">
                    <h3>Importer une archive ZIP</h3>
                    <p class="hint">
                        Même format que l’export « ZIP affiches locales »
                        (dossier <code>posters/</code> ou fichiers <code>123.jpg</code> à la racine).
                        Max. <?= (int) (MONCINE_POSTERS_ZIP_MAX_BYTES / 1024 / 1024) ?> Mo.
                    </p>
                    <form method="post" enctype="multipart/form-data" class="import-form">
                        <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                        <input type="hidden" name="action" value="import_posters_zip">
                        <label for="posters_zip">Archive ZIP</label>
                        <input type="file" name="posters_zip" id="posters_zip" accept=".zip,application/zip" required>
                        <button type="submit" class="btn btn-primary">Importer le ZIP</button>
                    </form>
                    <details class="import-columns-help">
                        <summary>Limites d’envoi du serveur (dépannage)</summary>
                        <p class="hint">
                            post_max_size = <strong><?= Moncine\View::escape((string) ($phpPostMaxSize ?? '?')) ?></strong>,
                            upload_max_filesize = <strong><?= Moncine\View::escape((string) ($phpUploadMaxSize ?? '?')) ?></strong>.
                            Pour un gros ZIP, il faut souvent au moins 85 Mo — mettre à jour le paquet Moncine
                            puis redémarrer PHP-FPM. Sinon copiez le dossier <code>posters/</code> en SSH à côté de
                            <code>moncine.db</code>.
                        </p>
                    </details>
                </div>
            <?php else: ?>
                <p class="hint">L’import ZIP est réservé à l’administrateur du site.</p>
            <?php endif; ?>
        </section>

        <?php if ($canManageCatalog): ?>
            <section class="<?= $panelClass('admin') ?>" id="import-panel-admin"
                     <?= $activeTab !== 'admin' ? ' hidden' : '' ?>>
                <p class="alert alert-info import-page__admin-banner">
                    <strong>Mode administrateur</strong> — catalogue partagé, enrichissement TMDB et outils de migration.
                </p>

                <div class="import-panel__block">
                    <h3>Exporter le catalogue</h3>
                    <?php if ((int) ($catalogCount ?? 0) === 0): ?>
                        <p class="hint">Catalogue vide.</p>
                    <?php else: ?>
                        <p class="stats"><?= (int) $catalogCount ?> œuvre(s).</p>
                        <p class="hint">
                            À importer sur une autre instance <strong>avant</strong> les bibliothèques utilisateurs.
                            Conservez la colonne <strong>ID catalogue</strong>.
                        </p>
                        <div class="export-actions">
                            <form method="post" action="/export.php" class="inline-form">
                                <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                                <input type="hidden" name="scope" value="catalog">
                                <input type="hidden" name="format" value="csv">
                                <input type="hidden" name="return" value="/import.php?tab=admin">
                                <button type="submit" class="btn btn-secondary">CSV catalogue</button>
                            </form>
                            <form method="post" action="/export.php" class="inline-form">
                                <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                                <input type="hidden" name="scope" value="catalog">
                                <input type="hidden" name="format" value="ods">
                                <input type="hidden" name="return" value="/import.php?tab=admin">
                                <button type="submit" class="btn btn-secondary">ODS catalogue</button>
                            </form>
                            <form method="post" action="/export.php" class="inline-form">
                                <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                                <input type="hidden" name="scope" value="catalog">
                                <input type="hidden" name="format" value="zip">
                                <input type="hidden" name="return" value="/import.php?tab=admin">
                                <button type="submit" class="btn btn-secondary">ZIP affiches</button>
                            </form>
                        </div>
                        <details class="import-columns-help">
                            <summary>Colonnes export catalogue</summary>
                            <p class="hint"><?= Moncine\View::escape(Moncine\CatalogExportSchema::columnLabelsText()) ?></p>
                        </details>
                    <?php endif; ?>
                </div>

                <div class="import-panel__block">
                    <h3>Importer le catalogue (migration)</h3>
                    <p class="hint">
                        Fichier « export catalogue » uniquement. Pour une migration depuis une autre instance Moncine,
                        cochez la réinitialisation ci-dessous afin de conserver les numéros d’ID.
                    </p>
                    <form method="post" enctype="multipart/form-data" class="import-form">
                        <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                        <input type="hidden" name="import_scope" value="catalog">
                        <label for="catalog_csv_file">Fichier CSV ou ODS (catalogue)</label>
                        <input type="file" name="csv_file" id="catalog_csv_file" accept=".csv,.ods,text/csv" required>
                        <label class="checkbox">
                            <input type="checkbox" name="replace_catalog" value="1">
                            Réinitialiser le catalogue avant import (conserve les ID du fichier)
                        </label>
                        <button type="submit" class="btn btn-primary">Importer le catalogue</button>
                    </form>
                </div>

                <div class="import-panel__block">
                    <h3>Enrichissement TMDB</h3>
                    <p class="hint">
                        Complète synopsis, affiche et acteurs via
                        <a href="https://www.themoviedb.org/settings/api" target="_blank" rel="noopener">themoviedb.org</a>.
                    </p>

                    <?php if (!$hasTmdbKey): ?>
                        <form method="post" action="/enrichir.php" class="import-form">
                            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                            <input type="hidden" name="action" value="save_tmdb_key">
                            <label for="tmdb_api_key">Clé API TMDB</label>
                            <input type="password" name="tmdb_api_key" id="tmdb_api_key" required autocomplete="off"
                                   placeholder="ex. a1b2c3d4e5f6…">
                            <button type="submit" class="btn btn-secondary">Enregistrer la clé</button>
                        </form>
                    <?php else: ?>
                        <?php if (!empty($tmdbKeyFromEnvironment)): ?>
                            <p class="hint">Clé active via <code>MONCINE_TMDB_API_KEY</code> (serveur).</p>
                        <?php else: ?>
                            <p class="hint">Clé enregistrée sur le serveur.</p>
                        <?php endif; ?>

                        <form method="post" action="/enrichir.php" class="import-form enrich-actions">
                            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                            <input type="hidden" name="action" value="enrichir">
                            <p class="hint">
                                Environ <?= (int) $enrichBatchSize ?> œuvre(s) par clic
                                <?php if ((int) ($enrichPending ?? 0) > 0): ?>
                                    — <?= (int) $enrichPending ?> restante(s).
                                <?php endif; ?>
                            </p>
                            <div class="export-actions">
                                <button type="submit" class="btn btn-accent">Enrichir le catalogue</button>
                                <label class="checkbox">
                                    <input type="checkbox" name="force_all" value="1">
                                    Tout retraiter
                                </label>
                            </div>
                        </form>
                        <form method="post" action="/enrichir.php" class="inline-form">
                            <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                            <input type="hidden" name="action" value="test_tmdb">
                            <button type="submit" class="btn btn-secondary btn-sm">Tester TMDB</button>
                        </form>

                        <details class="import-columns-help tmdb-key-manage">
                            <summary>Gérer la clé API</summary>
                            <?php if (!empty($tmdbKeyFromEnvironment)): ?>
                                <p class="hint">
                                    Modifiez <code>MONCINE_TMDB_API_KEY</code> dans PHP-FPM (YunoHost), puis rechargez PHP-FPM.
                                </p>
                            <?php else: ?>
                                <form method="post" action="/enrichir.php" class="import-form">
                                    <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                                    <input type="hidden" name="action" value="save_tmdb_key">
                                    <label for="tmdb_api_key_replace">Nouvelle clé</label>
                                    <input type="password" name="tmdb_api_key" id="tmdb_api_key_replace" required autocomplete="off">
                                    <button type="submit" class="btn btn-secondary btn-sm">Remplacer</button>
                                </form>
                                <form method="post" action="/enrichir.php" class="inline-form tmdb-key-clear-form"
                                      onsubmit="return confirm('Supprimer la clé TMDB enregistrée ?');">
                                    <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                                    <input type="hidden" name="action" value="clear_tmdb_key">
                                    <button type="submit" class="btn btn-secondary btn-sm">Supprimer la clé</button>
                                </form>
                            <?php endif; ?>
                        </details>
                    <?php endif; ?>
                </div>

                <details class="import-panel__block import-columns-help">
                    <summary>Outils avancés — recaler les affiches</summary>
                    <p class="hint">
                        Si les fichiers image ont de mauvais numéros après un import catalogue sans conserver les ID,
                        envoyez l’<strong>export catalogue de l’ancienne instance</strong> (avec colonne ID).
                    </p>
                    <form method="post" enctype="multipart/form-data" class="import-form">
                        <?php require MONCINE_ROOT . '/templates/_csrf_field.php'; ?>
                        <input type="hidden" name="action" value="remap_posters">
                        <label for="remap_catalog_csv">Export catalogue (ancienne instance)</label>
                        <input type="file" name="remap_catalog_csv" id="remap_catalog_csv" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-secondary">Recaler les affiches</button>
                    </form>
                </details>
            </section>
        <?php endif; ?>
    </div>

    <details class="import-columns-help import-page__tech">
        <summary>Détails techniques (dépannage)</summary>
        <p class="hint">
            Moteur d’import serveur : <strong><?= Moncine\View::escape((string) ($importEngineBuild ?? '?')) ?></strong>
            — si cette valeur ne change pas après une mise à jour du paquet, le nouveau code n’est peut-être pas déployé.
        </p>
    </details>
</section>

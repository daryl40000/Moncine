<?php
/**
 * Import CSV de la dvdthèque + enrichissement TMDB.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';

use Moncine\Csrf;
use Moncine\FilmEnricher;
use Moncine\FilmRepository;
use Moncine\ImportCsv;
use Moncine\ImportOds;
use Moncine\TmdbConfig;
use Moncine\View;

$message = '';
$errors = [];
$tmdbMessage = '';
$enrichMessage = '';

if (isset($_GET['export_error'])) {
    $exportErr = (string) $_GET['export_error'];
    $errors[] = match ($exportErr) {
        'empty' => 'Aucun film à exporter. Importez d’abord vos films.',
        'format' => 'Format d’export non reconnu.',
        'failed' => 'Export impossible. Réessayez ou consultez les logs du serveur.',
        default => 'Export impossible.',
    };
}
if (isset($_GET['tmdb_key_saved'])) {
    $tmdbMessage = 'Clé API TMDB enregistrée — les synopsis seront récupérés en français.';
}
if (isset($_GET['tmdb_key_error'])) {
    $errors[] = 'Impossible d\'enregistrer la clé TMDB (vérifiez les droits sur le dossier data/).';
}
if (isset($_GET['tmdb_test'])) {
    $msg = (string) ($_GET['tmdb_test_msg'] ?? '');
    $tmdbMessage = ($_GET['tmdb_test'] === 'ok' ? '✓ ' : '✗ ') . $msg;
}
if (isset($_GET['enrich_done'])) {
    $processed = (int) ($_GET['processed'] ?? 0);
    $enriched = (int) ($_GET['enriched'] ?? 0);
    $notFound = (int) ($_GET['not_found'] ?? 0);
    $remaining = (int) ($_GET['remaining'] ?? 0);
    $enrichMessage = sprintf(
        'Lot traité : %d film(s), %d enrichi(s), %d introuvable(s) sur TMDB.',
        $processed,
        $enriched,
        $notFound
    );
    if (!empty($_SESSION['enrich_last_errors'])) {
        $errors = array_merge($errors, $_SESSION['enrich_last_errors']);
        unset($_SESSION['enrich_last_errors']);
        $enrichMessage .= ' Détail des erreurs ci-dessous.';
    }
    if ($remaining > 0) {
        $enrichMessage .= ' Il reste ' . $remaining . ' film(s) à traiter — relancez le bouton.';
    } else {
        $enrichMessage .= ' Enrichissement terminé pour tous vos films.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    if (!Csrf::validateFromPost($_POST)) {
        header('Location: /import.php?csrf_error=1');
        exit;
    }

    $replaceAll = isset($_POST['replace_all']);

    $uploadError = (int) ($_FILES['csv_file']['error'] ?? UPLOAD_ERR_NO_FILE);
    if (!isset($_FILES['csv_file']) || $uploadError !== UPLOAD_ERR_OK) {
        $errors[] = match ($uploadError) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (maximum '
                . (int) (MONCINE_CSV_MAX_BYTES / 1024 / 1024) . ' Mo).',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier sélectionné.',
            default => 'Erreur lors de l\'envoi du fichier.',
        };
    } elseif ((int) $_FILES['csv_file']['size'] > MONCINE_CSV_MAX_BYTES) {
        $errors[] = 'Fichier trop volumineux (maximum '
            . (int) (MONCINE_CSV_MAX_BYTES / 1024 / 1024) . ' Mo).';
    } else {
        $tmp = $_FILES['csv_file']['tmp_name'];

        if ($replaceAll) {
            (new FilmRepository())->deleteAll();
        }

        $ext = strtolower(pathinfo((string) ($_FILES['csv_file']['name'] ?? ''), PATHINFO_EXTENSION));
        if ($ext === 'ods') {
            $result = (new ImportOds())->importFromPath($tmp);
        } else {
            $result = (new ImportCsv())->importFromPath($tmp);
        }
        $message = sprintf(
            '%d film(s) importé(s) ou mis à jour. %d vision(s) enregistrée(s) dans l’historique.',
            $result['imported'],
            $result['vues']
        );
        $errors = array_merge($errors, $result['errors']);
    }
}

$filmCount = (new FilmRepository())->count();
$enrichPending = (new FilmEnricher())->countPending();
$hasTmdbKey = TmdbConfig::hasApiKey();

View::render('import', [
    'pageTitle' => 'Importer',
    'message' => $message,
    'errors' => $errors,
    'tmdbMessage' => $tmdbMessage,
    'enrichMessage' => $enrichMessage,
    'filmCount' => $filmCount,
    'enrichPending' => $enrichPending,
    'hasTmdbKey' => $hasTmdbKey,
    'enrichBatchSize' => MONCINE_ENRICH_BATCH_SIZE,
]);

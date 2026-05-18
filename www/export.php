<?php
/**
 * Téléchargement de la collection (CSV ou ODS) — POST + jeton CSRF uniquement.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';

use Moncine\Csrf;
use Moncine\ExportCollection;
use Moncine\FilmRepository;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /import.php');
    exit;
}

Csrf::rejectUnlessValid($_POST, '/import.php');

$format = strtolower(trim((string) ($_POST['format'] ?? '')));
if (!in_array($format, ['csv', 'ods'], true)) {
    header('Location: /import.php?export_error=format');
    exit;
}

if ((new FilmRepository())->count() === 0) {
    header('Location: /import.php?export_error=empty');
    exit;
}

try {
    $exporter = new ExportCollection();
    if ($format === 'csv') {
        $exporter->sendCsvDownload();
    } else {
        $exporter->sendOdsDownload();
    }
} catch (\Throwable) {
    header('Location: /import.php?export_error=failed');
}

exit;

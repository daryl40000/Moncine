<?php
/**
 * Version imprimable de Mes films (mêmes filtres et tri que /films.php).
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';

use Moncine\ContentKindFilter;
use Moncine\FamilyGroupService;
use Moncine\FilmRepository;
use Moncine\FoyerRepository;
use Moncine\PrintListHelper;
use Moncine\UserContext;
use Moncine\View;

if (!(new FilmRepository())->usesCatalogModel()) {
    header('Location: /films.php');
    exit;
}

$sortBy = (string) ($_GET['sort'] ?? 'titre');
$sortDir = (string) ($_GET['dir'] ?? 'asc');
$query = trim((string) ($_GET['q'] ?? ''));
$kindFilter = ContentKindFilter::normalize((string) ($_GET['kind'] ?? ''));

$repo = new FilmRepository();
$films = $repo->findAll($sortBy, $sortDir, $query, $kindFilter);
$totalCount = $repo->count();

$backUrl = View::filmsCollectionUrl($query, $sortBy, $sortDir, $kindFilter);

$foyerLabel = '';
$group = (new FamilyGroupService())->findGroupForUser(UserContext::currentUserId());
if ($group !== null) {
    $foyerLabel = trim((string) ($group['nom'] ?? ''));
} else {
    $foyerId = UserContext::currentFoyerId();
    if ($foyerId > 0) {
        $foyer = (new FoyerRepository())->findById($foyerId);
        $foyerLabel = $foyer !== null ? trim((string) ($foyer['nom'] ?? '')) : '';
    }
}

View::render('imprimer-films', [
    'layout' => 'print',
    'pageTitle' => 'Mes films — version imprimable',
    'films' => $films,
    'filterSummary' => PrintListHelper::collectionFilterSummary($query, $kindFilter, count($films), $totalCount),
    'sortSummary' => PrintListHelper::sortLabel($sortBy) . ' (' . PrintListHelper::sortDirectionLabel($sortDir) . ')',
    'foyerLabel' => $foyerLabel,
    'backUrl' => $backUrl,
]);

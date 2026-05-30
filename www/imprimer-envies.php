<?php
/**
 * Version imprimable de Mes envies (mêmes filtres et tri que /souhaits.php).
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';

use Moncine\FamilyGroupService;
use Moncine\FilmRepository;
use Moncine\GroupWishlistRepository;
use Moncine\PrintListHelper;
use Moncine\UserContext;
use Moncine\View;
use Moncine\WishlistScope;
use Moncine\WishlistTargetRepository;

if (!(new FilmRepository())->usesCatalogModel()) {
    header('Location: /films.php');
    exit;
}

$sortBy = (string) ($_GET['sort'] ?? 'titre');
$sortDir = (string) ($_GET['dir'] ?? 'asc');
$query = trim((string) ($_GET['q'] ?? ''));
$scope = WishlistScope::normalize((string) ($_GET['scope'] ?? WishlistScope::MINE));

$foyerId = UserContext::currentFoyerId();
$groupWishlist = new GroupWishlistRepository();
$canShowGroup = $groupWishlist->canShowGroupView($foyerId);
if ($scope === WishlistScope::GROUP && !$canShowGroup) {
    $scope = WishlistScope::MINE;
}

if ($scope === WishlistScope::GROUP && !isset($_GET['sort'])) {
    $sortBy = 'votes';
    $sortDir = 'desc';
}

$repo = new FilmRepository();
$userId = UserContext::currentUserId();
$isGroupScope = $scope === WishlistScope::GROUP;

if ($isGroupScope) {
    $films = $groupWishlist->findAggregated($foyerId, $userId, $sortBy, $sortDir, $query);
} else {
    $films = $repo->findAllWishlist($sortBy, $sortDir, $query);
}

$group = $canShowGroup ? (new FamilyGroupService())->findGroupForUser($userId) : null;
$groupName = (string) ($group['nom'] ?? '');

$wishlistTargetsByFilmId = [];
if (!$isGroupScope && WishlistTargetRepository::tableExists() && $films !== []) {
    $ids = array_map(static fn (array $f): int => (int) ($f['id'] ?? 0), $films);
    $wishlistTargetsByFilmId = (new WishlistTargetRepository())->mapByBibliothequeIds($ids);
}

$backUrl = View::wishlistUrl($query, $sortBy, $sortDir, $scope);
$pageTitle = $isGroupScope
    ? 'Envies du groupe — version imprimable'
    : 'Mes envies — version imprimable';

View::render('imprimer-envies', [
    'layout' => 'print',
    'pageTitle' => $pageTitle,
    'films' => $films,
    'filterSummary' => PrintListHelper::wishlistFilterSummary($query, count($films), $isGroupScope, $groupName),
    'sortSummary' => PrintListHelper::sortLabel($sortBy) . ' (' . PrintListHelper::sortDirectionLabel($sortDir) . ')',
    'isGroupScope' => $isGroupScope,
    'wishlistTargetsByFilmId' => $wishlistTargetsByFilmId,
    'backUrl' => $backUrl,
]);

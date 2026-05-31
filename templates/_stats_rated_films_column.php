<?php
/**
 * Colonne de classement par note (statistiques).
 *
 * @var string $columnTitle
 * @var list<array<string, mixed>> $ratedFilms
 */
?>
<div class="stats-rated-column">
    <h3 class="stats-subtitle"><?= Moncine\View::escape($columnTitle) ?></h3>
    <?php if ($ratedFilms === []): ?>
        <p class="hint">Aucun film noté pour l’instant.</p>
    <?php else: ?>
        <ol class="stats-ranked-list">
            <?php foreach ($ratedFilms as $film): ?>
                <li>
                    <a href="/film.php?id=<?= (int) $film['id'] ?>" class="stats-ranked-list__link">
                        <?= Moncine\View::escape((string) $film['titre']) ?>
                    </a>
                    <?php if (trim((string) ($film['realisateur'] ?? '')) !== ''): ?>
                        <span class="stats-ranked-list__meta">— <?= Moncine\View::escape((string) $film['realisateur']) ?></span>
                    <?php endif; ?>
                    <span class="tag tag--note"><?= (int) ($film['best_note'] ?? 0) ?>/10</span>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>

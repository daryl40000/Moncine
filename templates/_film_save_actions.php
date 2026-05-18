<?php
/**
 * Boutons d’enregistrement (simple ou avec enrichissement TMDB).
 *
 * @var string $cancelUrl
 * @var bool $hasTmdbKey
 * @var string $submitLabel
 * @var string $enrichLabel
 */
$cancelUrl = $cancelUrl ?? '/films.php';
$hasTmdbKey = $hasTmdbKey ?? false;
$submitLabel = $submitLabel ?? 'Enregistrer le film';
$enrichLabel = $enrichLabel ?? 'Enregistrer avec enrichissement';
?>
<div class="form-actions form-actions--split">
    <button type="submit" name="save_mode" value="save" class="btn btn-primary">
        <?= Moncine\View::escape($submitLabel) ?>
    </button>
    <?php if ($hasTmdbKey): ?>
        <button type="submit" name="save_mode" value="enrich" class="btn btn-accent">
            <?= Moncine\View::escape($enrichLabel) ?>
        </button>
    <?php endif; ?>
    <a href="<?= Moncine\View::escape($cancelUrl) ?>" class="btn btn-ghost">Annuler</a>
    <p class="hint form-actions__hint">
        <?php if ($hasTmdbKey): ?>
            « <?= Moncine\View::escape($enrichLabel) ?> » enregistre la fiche puis complète synopsis, affiche,
            année, genres et acteurs via TMDB (selon le titre et la catégorie).
        <?php else: ?>
            <a href="/import.php">Configurez une clé API TMDB</a> pour activer l’enrichissement à l’enregistrement.
        <?php endif; ?>
    </p>
</div>

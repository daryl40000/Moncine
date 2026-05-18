<?php
/**
 * Détecte le type d’import (bibliothèque légère, catalogue admin, ancien export complet).
 */

declare(strict_types=1);

namespace Moncine;

final class ImportFormat
{
    public const KIND_LIBRARY = 'library';
    public const KIND_CATALOG = 'catalog';
    public const KIND_LEGACY = 'legacy';

    /**
     * @param list<string|null> $header
     */
    public static function detectFromHeader(array $header): string
    {
        $libraryMap = ImportFilmRows::mapHeaders($header, LibraryExportSchema::COLUMN_ALIASES);
        $catalogMap = ImportFilmRows::mapHeaders($header, CatalogExportSchema::COLUMN_ALIASES);
        $legacyMap = ImportFilmRows::mapHeaders($header, CollectionExportSchema::FILM_COLUMN_ALIASES);

        $hasOeuvreId = isset($libraryMap['oeuvre_id']) || isset($catalogMap['oeuvre_id']);
        $hasCatalogMeta = isset($catalogMap['synopsis'])
            || isset($catalogMap['poster_url'])
            || isset($catalogMap['duree_min'])
            || isset($catalogMap['styles']);
        $hasLibraryOnly = isset($libraryMap['statut'])
            || isset($libraryMap['support_physique'])
            || isset($libraryMap['format_image']);

        if ($hasOeuvreId && $hasCatalogMeta) {
            return self::KIND_CATALOG;
        }

        if ($hasOeuvreId && ($hasLibraryOnly || !$hasCatalogMeta)) {
            return self::KIND_LIBRARY;
        }

        if (isset($legacyMap['titre'])) {
            return self::KIND_LEGACY;
        }

        return self::KIND_LEGACY;
    }

    public static function label(string $kind): string
    {
        return match ($kind) {
            self::KIND_LIBRARY => 'bibliothèque (léger)',
            self::KIND_CATALOG => 'catalogue partagé',
            default => 'export complet (ancien format)',
        };
    }

    /**
     * Infos affichées après import pour comprendre pourquoi les ID changent ou non.
     *
     * @param list<string|null> $header
     * @return array{format: string, has_id_column: bool, label: string}
     */
    public static function analyzeHeader(array $header): array
    {
        $format = self::detectFromHeader($header);
        $catalogMap = ImportFilmRows::mapHeaders($header, CatalogExportSchema::COLUMN_ALIASES);
        $legacyMap = ImportFilmRows::mapHeaders($header, CollectionExportSchema::FILM_COLUMN_ALIASES);
        $hasId = isset($catalogMap['oeuvre_id']) || isset($legacyMap['oeuvre_id']);

        return [
            'format' => $format,
            'has_id_column' => $hasId,
            'label' => self::label($format),
        ];
    }
}

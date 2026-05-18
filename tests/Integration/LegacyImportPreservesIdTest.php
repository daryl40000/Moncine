<?php

declare(strict_types=1);

namespace Moncine\Tests\Integration;

use Moncine\CollectionExportSchema;
use Moncine\ImportFilmRows;
use Moncine\ImportFormat;
use Moncine\ImportRunner;
use Moncine\OeuvreRepository;
use Moncine\Tests\Support\MoncineTestCase;

final class LegacyImportPreservesIdTest extends MoncineTestCase
{
    public function testLegacyImportWithIdColumnAndReplaceCatalog(): void
    {
        $this->loginAsAdmin();

        $admin = new \Moncine\CatalogAdmin();
        $admin->importOeuvreFromExport(['titre' => 'A supprimer', 'realisateur' => 'X'], ['titre', 'realisateur']);

        $headers = CollectionExportSchema::filmHeaders();
        $map = ImportFilmRows::mapHeaders($headers, CollectionExportSchema::FILM_COLUMN_ALIASES);
        $this->assertArrayHasKey('oeuvre_id', $map);

        $row = array_fill(0, count($headers), '');
        $row[$map['oeuvre_id']] = '5010';
        $row[$map['titre']] = 'Film legacy ID';
        $row[$map['realisateur']] = 'Y';
        $row[$map['synopsis']] = 'Texte';

        $analysis = ImportFormat::analyzeHeader($headers);
        $this->assertSame(ImportFormat::KIND_CATALOG, $analysis['format']);
        $this->assertTrue($analysis['has_id_column']);

        $result = (new ImportRunner())->importFilmsSheet([$row], $headers, true);

        $this->assertTrue($result['catalog_cleared'] ?? false);
        $this->assertSame(1, $result['imported']);
        $this->assertNotNull((new OeuvreRepository())->findById(5010));
        $this->assertNull((new OeuvreRepository())->findByTitreAndRealisateur('A supprimer', 'X'));
    }
}

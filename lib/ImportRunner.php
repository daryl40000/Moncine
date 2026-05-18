<?php
/**
 * Importe les feuilles Films et Historique (CSV ou ODS export Moncine).
 */

declare(strict_types=1);

namespace Moncine;

final class ImportRunner
{
    public function __construct(
        private readonly FilmRepository $films = new FilmRepository(),
        private readonly HistoriqueRepository $historique = new HistoriqueRepository()
    ) {
    }

    /**
     * @param list<list<string|null>> $dataRows lignes sans l’en-tête
     * @param list<string|null> $header
     * @return array{imported: int, vues: int, errors: list<string>}
     */
    public function importFilmsSheet(array $dataRows, array $header): array
    {
        $map = ImportFilmRows::mapHeaders($header);
        if (!isset($map['titre'])) {
            return [
                'imported' => 0,
                'vues' => 0,
                'errors' => ['Colonne « Titre » introuvable dans l’en-tête.'],
            ];
        }

        $imported = 0;
        $vues = 0;
        $errors = [];
        $line = 1;

        foreach ($dataRows as $row) {
            $line++;
            if (ImportFilmRows::isEmptyRow($row)) {
                continue;
            }
            try {
                $parsed = ImportFilmRows::rowToFilm($row, $map);
                $titre = (string) $parsed['titre'];
                if ($titre === '') {
                    continue;
                }

                $vuRaw = (string) ($parsed['_vu'] ?? '');
                $noteRaw = (string) ($parsed['_note'] ?? '');
                $importColumns = (array) ($parsed['_import_columns'] ?? array_keys($map));
                unset($parsed['_vu'], $parsed['_note'], $parsed['_import_columns']);

                $this->films->upsertFromExport($parsed, $importColumns);
                $imported++;

                $film = $this->films->findByTitreAndRealisateur(
                    $titre,
                    (string) ($parsed['realisateur'] ?? '')
                );
                if ($film === null) {
                    continue;
                }

                $filmId = (int) $film['id'];
                $dateVue = ImportCsv::parseVueDate($vuRaw);
                if ($dateVue !== null) {
                    $note = ImportCsv::parseNote($noteRaw);
                    if ($this->historique->recordViewing($filmId, $dateVue, $note)) {
                        $vues++;
                    }
                }
            } catch (\Throwable $e) {
                $errors[] = 'Ligne ' . $line . ' ('
                    . ImportFilmRows::previewTitre($row, $map) . ') : ' . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'vues' => $vues, 'errors' => $errors];
    }

    /**
     * Feuille Historique de l’export ODS (toutes les visions).
     *
     * @param list<list<string|null>> $dataRows
     * @param list<string|null> $header
     * @return array{vues: int, errors: list<string>}
     */
    public function importHistoriqueSheet(array $dataRows, array $header): array
    {
        $map = ImportFilmRows::mapHeaders($header, ImportFilmRows::HISTORIQUE_MAP);
        if (!isset($map['titre'], $map['date_vue'])) {
            return [
                'vues' => 0,
                'errors' => ['Feuille Historique : colonnes « Titre » et « Date vue » requises.'],
            ];
        }

        $vues = 0;
        $errors = [];
        $line = 1;

        foreach ($dataRows as $row) {
            $line++;
            if (ImportFilmRows::isEmptyRow($row)) {
                continue;
            }
            try {
                $titre = ImportFilmRows::getCell($row, $map, 'titre');
                if ($titre === '') {
                    continue;
                }
                $realisateur = ImportFilmRows::getCell($row, $map, 'realisateur');
                $film = $this->films->findByTitreAndRealisateur($titre, $realisateur);
                if ($film === null) {
                    $errors[] = 'Historique ligne ' . $line . ' : film introuvable « ' . $titre . ' ».';
                    continue;
                }

                $dateIso = ImportCsv::parseVueDate(ImportFilmRows::getCell($row, $map, 'date_vue'));
                if ($dateIso === null) {
                    $errors[] = 'Historique ligne ' . $line . ' : date invalide.';
                    continue;
                }

                $note = ImportCsv::parseNote(ImportFilmRows::getCell($row, $map, 'note'));
                if ($this->historique->recordViewing((int) $film['id'], $dateIso, $note)) {
                    $vues++;
                }
            } catch (\Throwable $e) {
                $errors[] = 'Historique ligne ' . $line . ' : ' . $e->getMessage();
            }
        }

        return ['vues' => $vues, 'errors' => $errors];
    }

    /**
     * @param array{imported: int, vues: int, errors: list<string>} $a
     * @param array{imported?: int, vues?: int, errors?: list<string>} $b
     * @return array{imported: int, vues: int, errors: list<string>}
     */
    public static function mergeResults(array $a, array $b): array
    {
        return [
            'imported' => ($a['imported'] ?? 0) + ($b['imported'] ?? 0),
            'vues' => ($a['vues'] ?? 0) + ($b['vues'] ?? 0),
            'errors' => array_merge($a['errors'] ?? [], $b['errors'] ?? []),
        ];
    }
}

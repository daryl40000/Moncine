<?php
/**
 * Point d'entrée commun : charge la config et prépare l'autoload simple.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

// Autoload minimal pour les classes Moncine\*
spl_autoload_register(static function (string $class): void {
    $prefix = 'Moncine\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/' . $relative . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

// Vérifie PDO SQLite et le dossier data avant toute requête
Moncine\Requirements::abortIfNeeded();

// Session web (questionnaire + connexion)
Moncine\QuizSession::start();

// Initialise la base au premier accès
Moncine\Database::getInstance();

// Connexion obligatoire (sauf premier compte, login, assets)
if (PHP_SAPI !== 'cli') {
    Moncine\SecurityHeaders::send();
    Moncine\Auth::enforceWebAccess();
}

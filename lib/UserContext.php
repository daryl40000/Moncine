<?php
/**
 * Utilisateur courant (session de connexion).
 */

declare(strict_types=1);

namespace Moncine;

final class UserContext
{
  /** @deprecated Conservé pour compatibilité lecture seule ; utiliser Auth::currentUserId(). */
    public const DEFAULT_USER_ID = 1;

    public static function currentUserId(): int
    {
        $id = Auth::currentUserId();
        if ($id > 0) {
            return $id;
        }

        if (Auth::needsSetup()) {
            return 0;
        }

        Auth::enforceWebAccess();
        exit;
    }

    public static function canManageCatalog(): bool
    {
        return Auth::isAdmin();
    }
}

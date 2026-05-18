<?php
/**
 * Rôles des comptes Moncine.
 */

declare(strict_types=1);

namespace Moncine;

final class UserRole
{
    public const ADMIN = 'admin';
    public const USER = 'user';

    public static function normalize(string $raw): string
    {
        return match (mb_strtolower(trim($raw), 'UTF-8')) {
            self::ADMIN => self::ADMIN,
            default => self::USER,
        };
    }

    public static function isAdmin(string $role): bool
    {
        return self::normalize($role) === self::ADMIN;
    }

    public static function label(string $role): string
    {
        return match (self::normalize($role)) {
            self::ADMIN => 'Administrateur',
            default => 'Utilisateur',
        };
    }
}

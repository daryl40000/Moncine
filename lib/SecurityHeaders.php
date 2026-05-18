<?php
/**
 * En-têtes HTTP de sécurité pour les pages web.
 */

declare(strict_types=1);

namespace Moncine;

final class SecurityHeaders
{
    public static function send(): void
    {
        if (PHP_SAPI === 'cli' || headers_sent()) {
            return;
        }

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}

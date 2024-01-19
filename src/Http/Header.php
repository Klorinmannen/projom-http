<?php

declare(strict_types=1);

namespace Projom\Http;

class Header
{
    public static function parseAuthHeader(string $authHeader): ?string
    {
        if (!$authHeader)
            return null;

        $authHeader = trim($authHeader);
        if (strpos($authHeader, 'Bearer') === false)
            return null;

        $token = str_replace('Bearer', '', $authHeader);
        return trim($token);
    }
}

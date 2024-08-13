<?php

declare(strict_types=1);

namespace Projom\Http;

class Header
{
    public static function parseBearerAuthToken(string $authHeader): null|string
    {
        if (!$authHeader)
            return null;

        $authHeader = trim($authHeader);
        if (strpos($authHeader, 'Bearer') === false)
            return null;

        $authToken = str_replace('Bearer', '', $authHeader);
        $bearerAuthToken = trim($authToken);

        return $bearerAuthToken;
    }
}

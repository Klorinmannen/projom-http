<?php

declare(strict_types=1);

namespace Projom\Http\Auth\Jwt;

use Projom\Util\Base64;
use Projom\Util\Json;

class Util
{
    public static function encodeBase64url(array $data): string
    {
        if (!$data)
            return '';

        if (!$jsonString = Json::encode($data))
            return '';

        return Base64::encodeUrl($jsonString);
    }

    public static function decodeBase64url(string $base64url): array
    {
        if (!$base64url)
            return [];

        if (!$jsonString = Base64::decodeUrl($base64url))
            return [];

        $result = Json::verifyAndDecode($jsonString);
        if ($result === null)
            return [];

        return $result;
    }
}

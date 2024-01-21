<?php

declare(strict_types=1);

namespace Projom\Http\Auth\Jwt;

use Projom\Http\Auth\Jwt\Util;

class Header
{
    public static function create(
        string $alg,
        string $type
    ): string {

        $header = [
            'alg' => $alg,
            'typ' => $type
        ];

        return Util::encodeBase64url($header);
    }
}

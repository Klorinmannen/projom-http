<?php

declare(strict_types=1);

namespace Projom\Http\Auth\Jwt;

use Projom\Http\Auth\Jwt\Util;
use Projom\Util\Math\Expression as MathExpression;

class Payload
{
    public static function create(
        string $issuer,
        int $userID,
        string $expirationExpression
    ): string {

        $expirationSeconds = MathExpression::eval($expirationExpression);
        $issuedAt = strtotime('now');
        $exp = $issuedAt + $expirationSeconds;


        $payload = [
            'iss' => $issuer,
            'sub' => $userID,
            'iat' => $issuedAt,
            'exp' => $exp
        ];

        return Util::encodeBase64url($payload);
    }
}

<?php

declare(strict_types=1);

namespace Projom\Http\Auth\Jwt;

class Signature
{
    public static function create(
        string $base64urlHeader,
        string $base64urlPayload,
        string $alg,
        string $jwtKey
    ): string {

        $data = $base64urlHeader . '.' . $base64urlPayload;
        return hash_hmac($alg, $data, $jwtKey);
    }

    public static function compare(
        string $signature_1,
        string $signature_2
    ): bool {
        return hash_equals($signature_1, $signature_2);
    }
}

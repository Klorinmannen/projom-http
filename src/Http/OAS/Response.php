<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

class Response
{
    public static function normalize(array $expectedResponse): array
    {
        $normalized = [];
        foreach ($expectedResponse as $statusCode => $responseContract)
            $normalized[$statusCode] = key($responseContract['content'] ?? []);
        return $normalized;
    }

    public static function verify(int $statusCode, string $contentType, array $expectedResponse): bool
    {
        if (!$expectedResponseContentType = $expectedResponse[$statusCode] ?? '')
            return false;

        if ($expectedResponseContentType !== $contentType)
            return false;

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Projom\Http\Response;

use Projom\Http\ContentType;

class Header
{
    const APPLICATION_JSON = 'Content-Type: application/json; charset=utf-8';
    const TEXT_HTML = 'Content-Type: text/html; charset=utf-8';
    const TEXT_PLAIN = 'Content-Type: text/plain; charset=utf-8';
    const TEXT_CSS = 'Content-Type: text/css; charset=utf-8';
    const TEXT_JAVASCRIPT = 'Content-Type: text/javascript; charset=utf-8';
    const TEXT_CSV = 'Content-Type: text/csv; charset=utf-8';

    public static function convert(string $contentType): string
    {
        return match ($contentType) {
            ContentType::APPLICATION_JSON => static::APPLICATION_JSON,
            ContentType::TEXT_HTML => static::TEXT_HTML,
            ContentType::TEXT_PLAIN => static::TEXT_PLAIN,
            ContentType::TEXT_CSS => static::TEXT_CSS,
            ContentType::TEXT_JAVASCRIPT => static::TEXT_JAVASCRIPT,
            ContentType::TEXT_CSV => static::TEXT_CSV,
            default => throw new \Exception('Invalid content type', 400),
        };
    }
}

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
        switch ($contentType) {
            case ContentType::APPLICATION_JSON:
                return static::APPLICATION_JSON;

            case ContentType::TEXT_HTML:
                return static::TEXT_HTML;

            case ContentType::TEXT_PLAIN:
                return static::TEXT_PLAIN;

            case ContentType::TEXT_CSS:
                return static::TEXT_CSS;

            case ContentType::TEXT_JAVASCRIPT:
                return static::TEXT_JAVASCRIPT;

            case ContentType::TEXT_CSV:
                return static::TEXT_CSV;

            default: // Refactor: More specific exception.
                throw new \Exception('Invalid content type', 500);
        }
    }
}

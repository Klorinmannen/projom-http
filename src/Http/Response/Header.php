<?php

declare(strict_types=1);

namespace Projom\Http\Response;

use Projom\Http\ContentType;

class Header
{
    const APPLICATION_JSON = 'Content-Type: application/json; charset=utf-8';
    const HTML_TEXT = 'Content-Type: text/html; charset=utf-8';
    const PLAIN_TEXT = 'Content-Type: text/plain; charset=utf-8';
    const CSS_TEXT = 'Content-Type: text/css; charset=utf-8';
    const JAVASCRIPT_TEXT = 'Content-Type: text/javascript; charset=utf-8';
    const CSV_TEXT = 'Content-Type: text/csv; charset=utf-8';

    public static function build(string $contentType): string
    {
        switch ($contentType) {
            case ContentType::APPLICATION_JSON:
                return static::APPLICATION_JSON;

            case ContentType::HTML_TEXT:
                return static::HTML_TEXT;

            case ContentType::PLAIN_TEXT:
                return static::PLAIN_TEXT;

            case ContentType::CSS_TEXT:
                return static::CSS_TEXT;

            case ContentType::JAVASCRIPT_TEXT:
                return static::JAVASCRIPT_TEXT;

            case ContentType::CSV_TEXT:
                return static::CSV_TEXT;

            default: // Refactor: More specific exception.
                throw new \Exception('Invalid content type', 500);
        }
    }
}

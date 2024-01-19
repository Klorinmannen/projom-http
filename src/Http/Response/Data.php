<?php

declare(strict_types=1);

namespace Projom\Http\Response;

use Projom\Http\ContentType;
use Projom\Util\Json;

class Data
{
    public static function encode(
        array $data,
        string $contentType
    ): string {

        switch ($contentType) {
            case ContentType::APPLICATION_JSON:
                return Json::encode($data);

            // In this case its assumed that the data is already encoded.
            // All elements are strings that can be appended.
            case ContentType::HTML_TEXT:
            case ContentType::PLAIN_TEXT:
            case ContentType::CSS_TEXT:
            case ContentType::JAVASCRIPT_TEXT:
            case ContentType::CSV_TEXT:   
                return implode('', $data) ?? '';

            default: // Refactor: More specific exception.
                throw new \Exception('Invalid content type', 500);
        }
    }
}

<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Response\Data;
use Projom\Http\Response\Header;

class Response
{
    private readonly array $payload;
    private readonly int $statusCode;
    private readonly string $contentType;
    private readonly string $header;
    private readonly string $output;

    public function __construct(array $payload, int $statusCode, string $contentType)
    {
        $this->payload = $payload;
        $this->statusCode = $statusCode;
        $this->contentType  = $contentType;
        $this->header = Header::convert($contentType);
        $this->output = Data::encode($payload, $contentType);
    }

    public static function create(array $payload, int $statusCode = 200, string $contentType = 'application/json'): Response
    {
        return new Response($payload, $statusCode, $contentType);
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function contentType(): string
    {
        return $this->contentType;
    }

    public function header(): string
    {
        return $this->header;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header($this->header);
        echo $this->output;
    }
}

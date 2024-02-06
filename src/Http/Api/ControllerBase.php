<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Response;

/**
 * Base for resource controllers.
 */
abstract class ControllerBase
{
    private array $payload = [];
    private int $statusCode = 200;
    private string $contentType = 'application/json';

    abstract public function authorize(): bool;

    final public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    final public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    final public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    final public function response(): Response
    {
        return new Response(
            $this->payload,
            $this->statusCode,
            $this->contentType
        );
    }
}

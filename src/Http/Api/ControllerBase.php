<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Auth\Jwt;
use Projom\Http\Response;

/**
 * Base for resource controllers.
 */
abstract class ControllerBase
{
    protected array $payload = [];
    protected int $statusCode = 200;
    protected string $contentType = 'application/json';
    protected Jwt $jwt;

    final public function setJwt(Jwt $jwt): void
    {
        $this->jwt = $jwt;
    }

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

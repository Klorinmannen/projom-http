<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Response;
use Projom\Util\Json;

/**
 * Base for resource controllers
 */
abstract class ControllerBase
{
    private array $payload = [];
    private int $statusCode = 200;
    private string $contentType = 'application/json';

    protected readonly array $pathParameters;
    protected readonly array $queryParameters;
    protected readonly string $requestPayload;

    public function __construct(array $pathParameters, array $queryParameters, string $requestPayload)
    {
        $this->pathParameters = $pathParameters;
        $this->queryParameters = $queryParameters;
        $this->requestPayload = $requestPayload;
    }

    abstract public function authorize(): bool;

    final protected function decodePayloadJSON(): array
    {
        return Json::decode($this->requestPayload);
    }

    final protected function payload(string $key): mixed
    {
        return $this->requestPayload[$key] ?? null;
    }

    final protected function nextPathParameter(): mixed
    {
        return array_shift($this->pathParameters);
    }

    final protected function queryParameter(string $key): mixed
    {
        return $this->queryParameters[$key] ?? null;
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
        return Response::create($this->payload, $this->statusCode, $this->contentType);
    }
}

<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Request;

interface PathContractInterface
{
    public function verifyInputPathParameters(array $pathParameterList): bool;
    public function verifyInputQueryParameters(array $queryParameterList): bool;
    public function verifyInputPayload(string $payload): bool;
    public function verifyController(string $controllerBaseClass): bool;
    public function verifyResponse(int $statusCode, string $contentType): bool;
    public function hasAuth(): bool;
    public function controller(): string;
    public function operation(): string;
}

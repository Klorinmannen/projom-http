<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Request;
use Projom\Http\Response;

interface RouteContractInterface
{
    public function match(Request $request): bool;
    public function verifyInputData(Request $request): bool;
    public function verifyController(string $controllerBaseClass): bool;
    public function verifyResponse(Response $response): bool;
    public function hasAuth(): bool;
    public function controller(): string;
    public function operation(): string;
}

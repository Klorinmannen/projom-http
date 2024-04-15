<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Api\Oas\PayloadContract;
use Projom\Http\Api\Oas\ResponseContract;
use Projom\Http\Api\Oas\ParameterContract;
use Projom\Http\Api\PathContractInterface;

class PathContract implements PathContractInterface
{
    private ParameterContract|null $parameterContract = null;
    private PayloadContract|null $payloadContract = null;
    private ResponseContract|null $responseContract = null;

    private Path $path;
    private string $resourceController = '';
    private string $operation = '';
    private bool $auth = true;

    public function __construct(Path $path)
    {
        $this->path = $path;
        $this->build();
    }

    public static function create(Path $path): PathContract
    {
        return new PathContract($path);
    }

    private function build(): void
    {
        $this->parameterContract = $this->path->parameterContract();
        $this->payloadContract = $this->path->payloadContract();
        $this->responseContract = $this->path->responseContract();
        $this->resourceController = $this->path->resourceController();
        $this->operation = $this->path->operation();
        $this->auth = $this->path->hasAuth();
    }

    public function verifyController(string $controllerBaseClass): bool
    {
        if (!class_exists($this->resourceController))
            return false;

        if (!method_exists($this->resourceController, $this->operation))
            return false;

        if (!is_subclass_of($this->resourceController, $controllerBaseClass))
            return false;

        return true;
    }

    public function verifyInputPathParameters(array $pathParameterList): bool
    {
        return $this?->parameterContract->verifyPath($pathParameterList) ?? false;
    }

    public function verifyInputQueryParameters(array $queryParameterList): bool
    {
        return $this?->parameterContract->verifyQuery($queryParameterList) ?? false;
    }

    public function verifyInputPayload(string $payload): bool
    {
        return $this?->payloadContract->verify($payload) ?? false;
    }

    public function verifyResponse(int $statusCode, string $contentType): bool
    {
        return $this?->responseContract->verify($statusCode, $contentType) ?? false;
    }

    public function controller(): string
    {
        return $this->resourceController;
    }

    public function operation(): string
    {
        return $this->operation;
    }

    public function hasAuth(): bool
    {
        return $this->auth;
    }
}

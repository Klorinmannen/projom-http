<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Api\RouteContractInterface;

class RouteContract implements RouteContractInterface
{
    private string $rotuePattern = '';
    private string $routeController = '';
    private array $pathContracts = [];

    private PathContract|null $pathContract = null;

    public function __construct(
        array $pathContracts,
        string $routePattern,
        string $routeController
    ) {
        $this->pathContracts = $pathContracts;
        $this->rotuePattern = $routePattern;
        $this->routeController = $routeController;
    }

    public function match(Request $request): bool
    {
        if (!$request->matchPattern($this->rotuePattern))
            return false;

        if (!$pathContract = $this->pathContracts[$request->httpMethod()] ?? null)
            return false;
    
        $this->pathContract = $pathContract;
            
        return true;
    }

    public function verifyInputData(Request $request): bool
    {
        if (!$this->pathContract)
            return false;
            
        if ($pathParameters = $request->pathParameterList())
            if (!$this->pathContract->verifyPathParameters($pathParameters))
                return false;

        if ($queryParameters = $request->queryParameterList())
            if (!$this->pathContract->verifyQueryParameters($queryParameters))
                return false;

        if ($payload = $request->payload())
            if (!$this->pathContract->verifyPayload($payload))
                return false;

        return true;
    }

    public function verifyController(string $controllerBaseClass): bool
    {
        if (!$this->pathContract)
            return false;

        if (!class_exists($this->routeController))
            return false;

        $operation = $this->pathContract->operation();
        if (!method_exists($this->routeController, $operation))
            return false;

        if (!is_subclass_of($this->routeController, $controllerBaseClass))
            return false;

        return true;
    }

    public function verifyResponse(Response $response): bool
    {
        if (!$this->pathContract)
            return false;

        return $this->pathContract->verifyResponse($response);
    }

    public function hasAuth(): bool
    {
        if (!$this->pathContract)
            return false;

        return $this->pathContract->auth();
    }

    public function controller(): string
    {
        return $this->routeController;
    }

    public function operation(): string
    {
        if (!$this->pathContract)
            return '';

        return $this->pathContract->operation();
    }
}

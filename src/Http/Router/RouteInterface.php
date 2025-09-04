<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Closure;

use Projom\Http\Middleware\MiddlewareInterface;
use Projom\Http\Router\Route\InputDefinitionInterface;

/**
 * Public interface for defining HTTP routes.
 */
interface RouteInterface
{
	public function get(string $controllerMethod = ''): InputDefinitionInterface;
	public function post(string $controllerMethod = ''): InputDefinitionInterface;
	public function put(string $controllerMethod = ''): InputDefinitionInterface;
	public function delete(string $controllerMethod = ''): InputDefinitionInterface;
	public function patch(string $controllerMethod = ''): InputDefinitionInterface;
	public function addMiddleware(MiddlewareInterface|Closure $middleware): void;
}

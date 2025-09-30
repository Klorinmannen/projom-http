<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Closure;

use Projom\Http\Middleware\MiddlewareInterface;
use Projom\Http\Router\Route\Input\DefinitionInterface;

/**
 * Public interface for defining HTTP routes.
 */
interface RouteInterface
{
	public function get(string $controllerMethod = ''): DefinitionInterface;
	public function post(string $controllerMethod = ''): DefinitionInterface;
	public function put(string $controllerMethod = ''): DefinitionInterface;
	public function delete(string $controllerMethod = ''): DefinitionInterface;
	public function patch(string $controllerMethod = ''): DefinitionInterface;
	public function addMiddleware(MiddlewareInterface|Closure $middleware): void;
}

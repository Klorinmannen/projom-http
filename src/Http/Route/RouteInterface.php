<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Closure;

use Projom\Http\MiddlewareInterface;
use Projom\Http\Route\DataInterface;

/**
 * Public interface for defining HTTP routes.
 */
interface RouteInterface
{
	public function get(string $controllerMethod = ''): DataInterface;
	public function post(string $controllerMethod = ''): DataInterface;
	public function put(string $controllerMethod = ''): DataInterface;
	public function delete(string $controllerMethod = ''): DataInterface;
	public function patch(string $controllerMethod = ''): DataInterface;
	public function addMiddleware(MiddlewareInterface|Closure $middleware): void;
}

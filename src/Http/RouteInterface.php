<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;

use Projom\Http\Route\DataInterface;

interface RouteInterface
{
	public function get(string $controllerMethod = ''): DataInterface;
	public function post(string $controllerMethod = ''): DataInterface;
	public function put(string $controllerMethod = ''): DataInterface;
	public function delete(string $controllerMethod = ''): DataInterface;
	public function group(array $methods): void;
	public function addMiddleware(MiddlewareInterface|Closure $middleware): void;
}

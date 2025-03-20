<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Projom\Http\Route\Handler;
use Projom\Http\Route\RouteObject;

class Router
{
	private array $routes = [];

	public function __construct(array $routes = [])
	{
		$this->routes = $routes;
	}

	public function addRoute(string $path, Handler $handler, Closure $routeDefinition): void
	{
		$this->routes[$path] = $routeDefinition(RouteObject::create($path, $handler));
	}

	public function dispatch(Request $request): void
	{
		$route = $this->match($request);
		$route->verify();
		$route->execute($request);
	}

	private function match(Request $request): RouteObject
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;

		throw new \Exception('Not found', 404);
	}
}

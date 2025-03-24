<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Exception;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Route;
use Projom\Http\Route\Handler;

class Router
{
	private array $routes = [];

	public function __construct() {}

	public function loadOAS(string $filePath): void
	{
		$routes = OAS::load($filePath);
		$this->routes = array_merge($this->routes, $routes);
		ksort($this->routes);
	}

	public function addRoute(string $path, Handler $handler, Closure $routeDefinition): void
	{
		$this->routes[$path] = $routeDefinition(Route::create($path, $handler));
	}

	public function dispatch(Request $request): void
	{
		$route = $this->match($request);
		$route->setup();
		$route->verify($request);
		$route->execute($request);
	}

	private function match(Request $request): RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;

		throw new Exception('Not found', 404);
	}
}

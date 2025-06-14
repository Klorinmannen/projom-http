<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Exception;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\MiddlewareInterface;
use Projom\Http\Route\Route;
use Projom\Http\Route\RouteBase;

class Router
{
	private array $routes = [];
	private array $middlewares = [];

	public function __construct() {}

	public function loadOAS(string $filePath): void
	{
		$routes = OAS::load($filePath);
		$this->routes = array_merge($this->routes, $routes);
		ksort($this->routes);
	}

	public function addMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares[] = $middleware;
	}

	/**
	 * Add a route to the router.
	 * 
	 * @param string $path The path of the route.
	 * @param string $controller The controller class name.
	 * @param Closure $routeDefinition A Closure that defines the route.
	 * 
	 * * Example: Router->addRoute('/users', User::class, function (RouteInterface $route) { $route->get(); });
	 */
	public function addRoute(string $path, string $controller, Closure $routeDefinition): void
	{
		$route = Route::create($path, $controller);
		$routeDefinition($route);
		$this->routes[$path] = $route;
	}

	public function dispatch(Request $request): void
	{
		$this->processMiddlewares($request);

		$route = $this->match($request);
		if ($route === null)
			throw new Exception('Not found', 404);

		$route->processMiddlewares($request);
		$route->setup();
		$route->verify($request);

		try {
			$route->execute($request);
		} catch (Response $response) {
			$this->processResponse($response);
		}
	}

	private function processMiddlewares(Request $request): void
	{
		foreach ($this->middlewares as $middleware)
			$middleware instanceof Closure
				? $middleware($request)
				: $middleware->process($request);
	}

	private function match(Request $request): null|RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;

		return null;
	}

	private function processResponse(Response $response): void
	{
		$response->send();
	}
}

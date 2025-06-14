<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\MiddlewareInterface;
use Projom\Http\Route\Route;
use Projom\Http\Route\RouteBase;

class Router
{
	private array $routes = [];
	private array $middlewares = [
		'before' => [],
		'after' => []
	];

	public function __construct() {}

	public function loadOAS(string $filePath): void
	{
		$routes = OAS::load($filePath);
		$this->routes = array_merge($this->routes, $routes);
		ksort($this->routes);
	}

	public function addBeforeRoutingMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares['before'][] = $middleware;
	}

	public function addAfterRoutingMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares['after'][] = $middleware;
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
		try {
			$this->processMiddlewares($this->middlewares['before'], $request);

			$route = $this->match($request);
			if ($route === null)
				Response::reject('Not found', StatusCode::NOT_FOUND);

			$this->processRoute($route, $request);
		} catch (Response $response) {
			$this->processResponse($response);
		}
	}

	private function processMiddlewares(array $middlewares, Request|Response $message): void
	{
		if (! $middlewares)
			return;

		foreach ($middlewares as $middleware)
			$middleware instanceof Closure
				? $middleware($message)
				: $middleware->process($message);
	}

	private function match(Request $request): null|RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;
		return null;
	}

	private function processRoute(RouteBase $route, Request $request): void
	{
		$route->processMiddlewares($request);
		$route->setup();
		$route->verify($request);
		$route->execute($request);
	}

	private function processResponse(Response $response): void
	{
		$this->processMiddlewares($this->middlewares['after'], $response);
		$response->send();
	}
}

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
	private array $beforeRoutingMiddlewares = [];
	private array $afterRoutingMiddlewares = [];

	public function __construct() {}

	public function loadOAS(string $filePath): void
	{
		$routes = OAS::load($filePath);
		$this->routes = array_merge($this->routes, $routes);
		ksort($this->routes);
	}

	public function addMiddlewareBeforeRouting(MiddlewareInterface|Closure $middleware): void
	{
		$this->beforeRoutingMiddlewares[] = $middleware;
	}

	public function addMiddlewareAfterRouting(MiddlewareInterface|Closure $middleware): void
	{
		$this->afterRoutingMiddlewares[] = $middleware;
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

	public function dispatch(Request|null $request = null): void
	{
		if ($request === null)
			$request = Request::create();

		try {
			$this->processMiddlewares($this->beforeRoutingMiddlewares, $request);
			$this->dispatchRequest($request);
		} catch (Response $response) {
			$this->processMiddlewares($this->afterRoutingMiddlewares, $request, $response);
			$response->send();
		}
	}

	private function processMiddlewares(array $middlewares, ...$args): void
	{
		if (! $middlewares)
			return;

		foreach ($middlewares as $middleware)
			$middleware instanceof Closure
				? $middleware(...$args)
				: $middleware->process(...$args);
	}

	private function dispatchRequest(Request $request): void
	{
		$route = $this->match($request);
		if ($route === null)
			Response::reject('Not found', StatusCode::NOT_FOUND);

		$route->dispatch($request);
	}

	private function match(Request $request): null|RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;
		return null;
	}
}

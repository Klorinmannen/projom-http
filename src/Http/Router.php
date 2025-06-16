<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\MiddlewareInterface;
use Projom\Http\Router\Middleware;
use Projom\Http\Router\MiddlewareContext;
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

	public function addMiddleware(
		MiddlewareInterface|Closure $middleware,
		MiddlewareContext $context = MiddlewareContext::BEFORE_MATCHING_ROUTE
	): void {
		$this->middlewares[] = Middleware::create($middleware, $context);
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
			$this->dispatchRequest($request);
		} catch (Response $response) {
			$this->dispatchResponse($request, $response);
		}
	}

	private function processMiddlewares(MiddlewareContext $context, object ...$args): void
	{
		$middlewares = array_filter(
			$this->middlewares,
			fn(Middleware $middleware) => $middleware->isContext($context)
		);

		foreach ($middlewares as $middleware)
			$middleware->process(...$args);
	}

	private function dispatchRequest(Request $request): void
	{
		$this->processMiddlewares(MiddlewareContext::BEFORE_MATCHING_ROUTE, $request);
		$route = $this->match($request);
		if ($route === null)
			Response::reject('Not found', StatusCode::NOT_FOUND);

		$this->processMiddlewares(MiddlewareContext::BEFORE_DISPATCHING_ROUTE, $request);
		$route->dispatch($request);
	}

	private function match(Request $request): null|RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;
		return null;
	}

	public function dispatchResponse(Request $request, Response $response): void
	{
		$this->processMiddlewares(MiddlewareContext::BEFORE_SENDING_RESPONSE, $request, $response);
		$response->send();
	}
}

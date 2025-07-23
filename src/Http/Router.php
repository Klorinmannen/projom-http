<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use ValueError;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\MiddlewareInterface;
use Projom\Http\StatusCode;
use Projom\Http\Route\Route;
use Projom\Http\Route\RouteBase;
use Projom\Http\Router\Middleware;
use Projom\Http\Router\MiddlewareContext;

class Router
{
	private array $routes = [];
	private array $middlewares = [];

	public function __construct() {}

	public function addMiddleware(
		MiddlewareInterface|Closure $middleware,
		MiddlewareContext $context = MiddlewareContext::BEFORE_MATCHING_ROUTE
	): void {
		$this->middlewares[] = Middleware::create($middleware, $context);
	}

	public function addRoutesFromOAS(string $filepath): void
	{
		$routes = OAS::load($filepath);
		$this->routes = array_merge($this->routes, $routes);
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
		if (!$path)
			throw new ValueError('Path cannot be empty');
		if (!$controller)
			throw new ValueError('Controller cannot be empty');

		$route = Route::create($path, $controller);
		$routeDefinition($route);
		$this->routes[] = $route;
	}

	public function dispatch(Request|null $request = null): void
	{
		if ($request === null)
			$request = Request::create();

		try {
			$this->dispatchRequest($request);
		} catch (Response $response) {
			$request->setResponse($response);
			$this->dispatchResponse($request);
		}
	}

	private function processMiddlewares(MiddlewareContext $context, Request $request): void
	{
		$middlewares = array_filter(
			$this->middlewares,
			fn(Middleware $middleware) => $middleware->isContext($context)
		);

		foreach ($middlewares as $middleware)
			$middleware->process($request);
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

	public function dispatchResponse(Request $request): void
	{
		$this->processMiddlewares(MiddlewareContext::BEFORE_SENDING_RESPONSE, $request);
		$request->response()->send();
	}
}

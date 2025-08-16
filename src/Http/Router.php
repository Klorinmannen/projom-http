<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use ValueError;

use Projom\Http\OAS;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Response\Code;
use Projom\Http\Route\Action;
use Projom\Http\Route\Route;
use Projom\Http\Route\RouteBase;
use Projom\Http\Router\Dispatcher;
use Projom\Http\Router\DispatcherInterface;
use Projom\Http\Router\Middleware;
use Projom\Http\Router\MiddlewareContext;
use Projom\Http\Router\MiddlewareInterface;

class Router
{
	private DispatcherInterface $dispatcher;
	private array $routes = [];
	private array $middlewares = [];

	public function __construct(DispatcherInterface $dispatcher = new Dispatcher())
	{
		$this->dispatcher = $dispatcher;
	}

	public function addMiddleware(
		MiddlewareInterface|Closure $middleware,
		MiddlewareContext $context = MiddlewareContext::BEFORE_ROUTING
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
	 * @param Closure $routeDefinition A Closure which defines the route.
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

	/**
	 * Route a request and return the action to be executed.
	 *
	 * @param Request|null $request The request to route.
	 * @return array [$controller: string, $method: string].
	 */
	public function route(null|Request $request = null): array
	{
		$action = $this->processRequest($request);
		return $action->get();
	}

	private function processRequest(null|Request $request): Action
	{
		if ($request === null)
			$request = Request::create();

		$action = $this->processRoutes($request);
		return $action;
	}

	private function processRoutes(Request $request): Action
	{
		try {
			$this->processMiddlewares(MiddlewareContext::BEFORE_ROUTING, $request);

			$route = $this->matchRoute($request);
			if ($route === null)
				Response::reject('Not found', Code::NOT_FOUND);

			$route->process($request);
		} catch (Response $response) {
			$response->send();
		}

		$action = $route->action();

		return $action;
	}

	private function matchRoute(Request $request): null|RouteBase
	{
		foreach ($this->routes as $route)
			if ($route->match($request))
				return $route;
		return null;
	}

	/**
	 * Route and dispatch the request to the matched route's action.
	 * Uses built-in Controller and method evocation.
	 * 
	 * @param null|Request $request The request to route and dispatch.
	 */
	public function dispatch(null|Request $request = null): void
	{
		$action = $this->processRequest($request);
		try {
			$this->processMiddlewares(MiddlewareContext::BEFORE_DISPATCHING, $request);
			$this->dispatcher->processAction($action, $request);
		} catch (ResponseBase $response) {
			$request->setResponse($response);
			$this->processMiddlewares(MiddlewareContext::AFTER_DISPATCHING, $request);
			$response->send();
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
}

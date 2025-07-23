<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Closure;

use Projom\Http\Method;
use Projom\Http\MiddlewareInterface;
use Projom\Http\Route\Data;
use Projom\Http\Route\DataInterface;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Path;
use Projom\Http\Route\RouteBase;
use Projom\Http\Route\RouteInterface;
use Projom\Http\Router\Middleware;

class Route extends RouteBase implements RouteInterface
{
	public static function create(string $path, string $controller): Route
	{
		$path = Path::create($path);
		$handler = Handler::create($controller);
		$route = new Route($path, $handler);
		return $route;
	}

	public function addMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares[] = Middleware::create($middleware);
	}

	public function get(string $controllerMethod = ''): DataInterface
	{
		return $this->addPath(Method::GET, $controllerMethod);
	}

	public function post(string $controllerMethod = ''): DataInterface
	{
		return $this->addPath(Method::POST, $controllerMethod);
	}

	public function put(string $controllerMethod = ''): DataInterface
	{
		return $this->addPath(Method::PUT, $controllerMethod);
	}

	public function delete(string $controllerMethod = ''): DataInterface
	{
		return $this->addPath(Method::DELETE, $controllerMethod);
	}

	public function patch(string $controllerMethod = ''): DataInterface
	{
		return $this->addPath(Method::PATCH, $controllerMethod);
	}

	private function addPath(Method $method, string $controllerMethod): DataInterface
	{
		$data = Data::create($method, $controllerMethod);
		$this->methodData[$method->name] = $data;
		return $data;
	}

	protected function setup(): void
	{
		$this->handler->setMethod($this->matchedData->method());
	}
}

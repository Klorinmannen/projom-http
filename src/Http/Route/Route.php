<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Closure;

use Projom\Http\Method;
use Projom\Http\MiddlewareInterface;
use Projom\Http\Route\RouteInterface;
use Projom\Http\Route\Data;
use Projom\Http\Route\DataInterface;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Path;
use Projom\Http\Route\RouteBase;
use Projom\Http\Router\Middleware;

class Route extends RouteBase implements RouteInterface
{
	public function __construct(string $path, string $controller)
	{
		$this->handler = Handler::create($controller);
		$this->path = Path::create($path);
	}

	public static function create(string $path, mixed $handler): Route
	{
		return new Route($path, $handler);
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

<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

use Closure;

use Projom\Http\Method;
use Projom\Http\Middleware\MiddlewareInterface;
use Projom\Http\Router\Middleware;
use Projom\Http\Router\RouteInterface;
use Projom\Http\Router\Route\Data;
use Projom\Http\Router\Route\DataInterface;
use Projom\Http\Router\Route\RouteBase;

class Route extends RouteBase implements RouteInterface
{
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
		$this->action->setMethod($this->matchedData->method());
	}
}

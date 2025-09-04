<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

use Closure;

use Projom\Http\Method;
use Projom\Http\Middleware\MiddlewareInterface;
use Projom\Http\Router\Middleware;
use Projom\Http\Router\RouteInterface;
use Projom\Http\Router\Route\InputDefinition;
use Projom\Http\Router\Route\InputDefinitionInterface;
use Projom\Http\Router\Route\RouteBase;

class Route extends RouteBase implements RouteInterface
{
	public function addMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares[] = Middleware::create($middleware);
	}

	public function get(string $controllerMethod = ''): InputDefinitionInterface
	{
		return $this->addPath(Method::GET, $controllerMethod);
	}

	public function post(string $controllerMethod = ''): InputDefinitionInterface
	{
		return $this->addPath(Method::POST, $controllerMethod);
	}

	public function put(string $controllerMethod = ''): InputDefinitionInterface
	{
		return $this->addPath(Method::PUT, $controllerMethod);
	}

	public function delete(string $controllerMethod = ''): InputDefinitionInterface
	{
		return $this->addPath(Method::DELETE, $controllerMethod);
	}

	public function patch(string $controllerMethod = ''): InputDefinitionInterface
	{
		return $this->addPath(Method::PATCH, $controllerMethod);
	}

	private function addPath(Method $method, string $controllerMethod): InputDefinitionInterface
	{
		$inputDefinition = InputDefinition::create($method, $controllerMethod);
		$this->inputDefinitions[$method->name] = $inputDefinition;
		return $inputDefinition;
	}

	protected function setup(): void
	{
		$this->action->setMethod($this->inputDefinition->method());
	}
}

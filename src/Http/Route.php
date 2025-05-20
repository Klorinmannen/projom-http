<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Exception;

use Projom\Http\Method;
use Projom\Http\RouteInterface;
use Projom\Http\Route\Data;
use Projom\Http\Route\DataInterface;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Parameter;
use Projom\Http\Route\Payload;

class Route extends RouteBase implements RouteInterface
{
	public function __construct(string $path, string $controller)
	{
		$this->handler = Handler::create($controller);
		$this->path = $path;
	}

	public function addMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		$this->middlewares[] = $middleware;
	}

	public static function create(string $path, mixed $handler): Route
	{
		return new Route($path, $handler);
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

	public function group(array $methods): void
	{
		foreach ($methods as $method)
			$this->addPath($method);
	}

	private function addPath(Method $method, string $controllerMethod = ''): DataInterface
	{
		$data = Data::create($method, $controllerMethod);
		$this->methodData[$method->name] = $data;
		return $data;
	}

	public function setup(): void
	{
		$this->handler->setMethod($this->matchedData->method());
	}

	protected function verifyData(Request $request): void
	{
		$expectedInput = $this->matchedData->expectedInput();

		if (! Payload::verify($request->payload(), $expectedInput['payload']))
			throw new Exception('Provided payload does not match expected', 400);

		$normalizedQueryParams = Parameter::normalize($expectedInput['query']);
		if (! Parameter::verifyQuery($request->queryParameters(), $normalizedQueryParams))
			throw new Exception('Provided query parameters does not match expected', 400);
	}
}

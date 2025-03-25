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
	public function __construct(string $path, Handler $handler)
	{
		$this->handler = $handler;
		$this->path = $path;
	}

	public function addMiddleware(MiddlewareInterface|Closure $middleware): void
	{
		array_unshift($this->middlewares, $middleware);
	}

	public static function create(string $path, mixed $handler): Route
	{
		return new Route($path, $handler);
	}

	public function get(null|Handler $handler = null): DataInterface
	{
		return $this->addPath(Method::GET, $handler);
	}

	public function post(null|Handler $handler = null): DataInterface
	{
		return $this->addPath(Method::POST, $handler);
	}

	public function put(null|Handler $handler = null): DataInterface
	{
		return $this->addPath(Method::PUT, $handler);
	}

	public function delete(null|Handler $handler = null): DataInterface
	{
		return $this->addPath(Method::DELETE, $handler);
	}

	public function group(array $methods): void
	{
		foreach ($methods as $method)
			$this->addPath($method, null);
	}

	private function addPath(Method $method, null|Handler $handler): DataInterface
	{
		$data = new Data($method, $handler);
		$this->methodData[$method->name] = $data;
		return $data;
	}

	public function setup(): void
	{
		if ($this->matchedData->hasHandler())
			$this->handler = $this->matchedData->handler();
		if ($this->handler->requiresDefaultMethod())
			$this->handler->setDefaultMethod($this->matchedData->method());
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

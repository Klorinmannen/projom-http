<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Path;
use Projom\Http\StatusCode;

abstract class RouteBase
{
	protected null|Path $path = null;
	protected null|Handler $handler = null;
	protected array $methodData = [];
	protected null|object $matchedData = null;
	protected array $middlewares = [];

	public function __construct(Path $path, null|Handler $handler = null)
	{
		$this->path = $path;
		$this->handler = $handler;
	}

	public static function create(string $path, null|string $controller = null): static
	{
		$path = Path::create($path);
		$handler = $controller !== null ? Handler::create($controller) : null;
		$route = new static($path, $handler);
		return $route;
	}

	public function match(Request $request): bool
	{
		[$result, $pathParameters] = $this->path->test($request->path());
		if ($result === false)
			return false;

		$method = $request->method();
		if (!$this->hasMethod($method))
			Response::reject('Method not allowed', StatusCode::METHOD_NOT_ALLOWED);

		$request->setPathParameters($pathParameters);
		$this->matchedData = $this->methodData[$method->name];

		return true;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->methodData);
	}

	public function dispatch(Request $request): void
	{
		$this->processMiddlewares($request);
		$this->setup();
		$this->verify($request);
		$this->execute($request);
	}

	private function processMiddlewares(Request $request): void
	{
		foreach ($this->middlewares as $middleware)
			$middleware->process($request);
	}

	abstract protected function setup(): void;

	private function verify(Request $request): void
	{
		if ($this->matchedData === null)
			Response::reject('Not found', StatusCode::NOT_FOUND);

		if ($this->handler === null)
			Response::abort('Route handler missing');

		$this->handler->verify();
		$this->matchedData->verify($request);
	}

	private function execute(Request $request): void
	{
		$this->handler->call($request);
	}
}

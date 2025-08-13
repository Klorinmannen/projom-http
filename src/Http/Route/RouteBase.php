<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Route\Action;
use Projom\Http\Route\Path;
use Projom\Http\StatusCode;

abstract class RouteBase
{
	protected null|Path $path = null;
	protected null|Action $action = null;
	protected array $methodData = [];
	protected null|object $matchedData = null;
	protected array $middlewares = [];

	public function __construct(Path $path, null|Action $action = null)
	{
		$this->path = $path;
		$this->action = $action;
	}

	public static function create(string $path, null|string $controller = null): static
	{
		$path = Path::create($path);
		$handler = $controller !== null ? Action::create($controller) : null;
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

	public function process(Request $request): void
	{
		$this->processMiddlewares($request);
		$this->setup();
		$this->verify($request);
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

		if ($this->action === null)
			Response::abort('Route action missing');

		$this->matchedData->verify($request);
	}

	public function action(): Action
	{
		return $this->action;
	}
}

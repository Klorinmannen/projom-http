<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Response\Code;
use Projom\Http\Router\Route\Action;
use Projom\Http\Router\Route\Input\Definition;
use Projom\Http\Router\Route\Path;

abstract class RouteBase
{
	protected null|Path $path = null;
	protected null|Action $action = null;
	protected array $inputDefinitions = [];
	protected null|object $inputDefinition = null;
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
			Response::reject('Method not allowed', Code::METHOD_NOT_ALLOWED);

		$request->setPathParameters($pathParameters);
		$this->inputDefinition = $this->inputDefinitions[$method->name];

		return true;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->inputDefinitions);
	}

	public function process(Request $request): void
	{
		$this->processMiddlewares($request);
		$this->setup();
		if (!$this->verify())
			Response::reject('Not found', Code::NOT_FOUND);
	}

	private function verify(): bool
	{
		if ($this->inputDefinition === null)
			return false;
		if ($this->action === null)
			return false;
		return true;
	}

	private function processMiddlewares(Request $request): void
	{
		foreach ($this->middlewares as $middleware)
			$middleware->process($request);
	}

	abstract protected function setup(): void;

	public function action(): Action
	{
		return $this->action;
	}

	public function inputDefinition(): null|Definition
	{
		return $this->inputDefinition;
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Exception;

use Projom\Http\Route\Handler;
use Projom\Http\Route\Pattern;

abstract class RouteBase
{
	protected string $routePath = '';
	protected null|Handler $handler = null;
	protected array $methodData = [];
	protected null|object $matchedData = null;
	protected array $middlewares = [];

	public function match(Request $request): bool
	{
		[$result, $matches] = $this->matchRoutePath($request->path());
		if ($result === false)
			return false;

		$method = $request->method();
		if (! $this->hasMethod($method))
			throw new Exception('Method not allowed', 405);

		// Remove the first element, the matching path string.
		$pathParameters = array_slice($matches, 1);
		$pathParameters = $this->keyPathParameters($pathParameters);
		$request->setPathParameters($pathParameters);

		$this->matchedData = $this->methodData[$method->name];
		return true;
	}

	private function matchRoutePath(string $requestPath): array
	{
		$pattern = Pattern::create($this->routePath);
		if (preg_match($pattern, $requestPath, $matches) === 0)
			return [false, []];
		return [true, $matches];
	}

	private function keyPathParameters(array $pathParameters): array
	{
		preg_match_all(Pattern::FIND_NAMES, $this->routePath, $matches);

		// Last element contains the parameter names.
		$pathParameterNames = array_pop($matches);

		return array_combine($pathParameterNames, $pathParameters);
	}

	/**
	 * Prepares the route path by adding a numeric identifier
	 * to the path parameters that are missing identifiers.
	 */
	protected function preparePath($path): string
	{
		$counter = 1;
		$routePath = preg_replace_callback(
			Pattern::PREPARE_ROUTE_PATH_NAMES,
			function ($matches) use (&$counter) {
				$type = $matches[1];
				$name = $matches[2] ?? $counter;
				$counter++;
				return "{{$type}:{$name}}";
			},
			$path
		);

		return $routePath;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->methodData);
	}

	public function processMiddlewares(Request $request): void
	{
		foreach ($this->middlewares as $middleware)
			$middleware instanceof Closure
				? $middleware($request)
				: $middleware->process($request);
	}

	abstract public function setup(): void;
	abstract protected function verifyData(Request $request): void;

	public function verify(Request $request): void
	{
		if (! $this->matchedData)
			throw new Exception('Not found', 404);

		if ($this->handler === null)
			throw new Exception('Route handler missing', 500);

		$this->handler->verify();

		$this->verifyData($request);
	}

	public function execute(Request $request): void
	{
		$this->handler->call($request);
	}
}

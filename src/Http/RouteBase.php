<?php

declare(strict_types=1);

namespace Projom\Http;

use Closure;
use Exception;

use Projom\Http\Route\Handler;
use Projom\Http\Route\Pattern;

abstract class RouteBase
{
	protected string $path = '';
	protected null|Handler $handler = null;
	protected array $methodData = [];
	protected null|object $matchedData = null;
	protected array $middlewares = [];

	public function match(Request $request): bool
	{
		$pattern = Pattern::create($this->path);
		if (preg_match($pattern, $request->path(), $matches) === 0)
			return false;

		$method = $request->method();
		if (! $this->hasMethod($method))
			throw new Exception('Method not allowed', 405);

		$request->setPathParameters(array_slice($matches, 1));
		$this->matchedData = $this->methodData[$method->name];

		return true;
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

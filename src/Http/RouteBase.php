<?php

declare(strict_types=1);

namespace Projom\Http;

use Exception;

use Projom\Http\Route\Handler;
use Projom\Http\Route\Pattern;

abstract class RouteBase
{
	protected string $path = '';
	protected null|Handler $handler = null;
	protected array $methodData = [];
	protected array $matched = [];

	public function match(Request $request): bool
	{
		$pattern = Pattern::create($this->path);
		if (preg_match($pattern, $request->path(), $matches) === 0)
			return false;

		$method = $request->method();
		if (! $this->hasMethod($method))
			throw new Exception('Method not allowed', 405);

		$data = $this->methodData[$method->name];

		$this->matched = [
			'method' => $method,
			'params' => [
				'path' => array_slice($matches, 1),
				'query' => $request->queryParameters(),
				'payload' => $request->payload()
			],
			'data' => $data,
		];

		return true;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->methodData);
	}

	abstract public function setup(): void;
	abstract protected function verifyData(): void;

	public function verify(): void
	{
		if (! $this->matched)
			throw new Exception('Not found', 404);

		if ($this->handler === null)
			throw new Exception('Route handler missing', 500);

		$this->handler->verify();

		$this->verifyData();
	}

	public function execute(): void
	{		
		$this->handler->call(...$this->matched['params']);
	}
}

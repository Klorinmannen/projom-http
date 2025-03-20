<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Route;
use Projom\Http\Route\Data;

class RouteObject implements Route
{
	private readonly string $path;
	private Handler $handler;
	private array $data = [];
	private array $matched = [];

	public function __construct(string $path, Handler $handler)
	{
		$this->handler = $handler;
		$this->path = $path;
	}

	public static function create(string $path, mixed $handler): RouteObject
	{
		return new RouteObject($path, $handler);
	}

	public function get(null|Handler $handler = null): Data
	{
		return $this->addPath(Method::GET, $handler);
	}

	public function post(null|Handler $handler = null): Data
	{
		return $this->addPath(Method::POST, $handler);
	}

	public function put(null|Handler $handler = null): Data
	{
		return $this->addPath(Method::PUT, $handler);
	}

	public function delete(null|Handler $handler = null): Data
	{
		return $this->addPath(Method::DELETE, $handler);
	}

	public function group(array $methods, null|Handler $handler = null): void
	{
		foreach ($methods as $method)
			$this->addPath($method, $handler);
	}

	private function addPath(Method $method, null|Handler $handler): Data
	{
		$data = new Data();
		$this->data[$method->name] = [
			$handler,
			$data
		];
		return $data;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->data);
	}

	public function match(Request $request): bool
	{
		$pattern = Pattern::create($this->path);
		if (preg_match($pattern, $request->urlPath(), $matches) === 0)
			return false;

		$method = $request->method();
		if (! $this->hasMethod($method))
			throw new \Exception('Method not allowed', 405);

		[$handler, $data] = $this->data[$method->name];

		if ($handler !== null)
			$this->handler = $handler;

		if ($this->handler->requiresDefaultMethod())
			$this->handler->setDefaultMethod($method);

		$this->matched = [
			'method' => $method->name,
			'params' => [
				array_slice($matches, 1),
				$request->queryParameters(),
				$request->payload()
			],
			'data' => $data,
		];

		return true;
	}

	public function verify(): void
	{
		if (! $this->matched)
			throw new \Exception('Not found', 404);

		if ($this->handler === null)
			throw new \Exception('Route handler missing', 500);

		$this->handler->verify();

		[$pathParams, $queryParams, $payload] = $this->matched['params'];
		$data = $this->matched['data'];
		if ($data->expectsPayload)
			if (! $payload)
				throw new \Exception('Provided payload does not match expected', 400);

		if ($data->expectsQueryParameters)
			if (! Parameter::verifyQuery($queryParams, $data->expectsQueryParameters))
				throw new \Exception('Provided query parameters does not match expected', 400);
	}

	public function execute(): void
	{
		$this->handler->call($this->matched['params']);
	}
}

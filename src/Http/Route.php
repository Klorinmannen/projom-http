<?php

declare(strict_types=1);

namespace Projom\Http;

use Exception;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\RouteInterface;
use Projom\Http\Route\Data;
use Projom\Http\Route\DataInterface;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Parameter;
use Projom\Http\Route\Pattern;
use Projom\Http\Route\Payload;

class Route implements RouteInterface
{
	private readonly string $path;
	private Handler $handler;
	private array $methodData = [];
	private array $matched = [];

	public function __construct(string $path, Handler $handler)
	{
		$this->handler = $handler;
		$this->path = $path;
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
		$data = new Data($handler);
		$this->methodData[$method->name] = $data;
		return $data;
	}

	private function hasMethod(Method $method): bool
	{
		return array_key_exists($method->name, $this->methodData);
	}

	public function match(Request $request): bool
	{
		$pattern = Pattern::create($this->path);
		if (preg_match($pattern, $request->urlPath(), $matches) === 0)
			return false;

		$method = $request->method();
		if (! $this->hasMethod($method))
			throw new Exception('Method not allowed', 405);

		$data = $this->methodData[$method->name];

		if ($data->hasHandler())
			$this->handler = $data->handler();

		if ($this->handler->requiresDefaultMethod())
			$this->handler->setDefaultMethod($method);

		$this->matched = [
			'method' => $method->name,
			'params' => [
				'path' => array_slice($matches, 1),
				'query' => $request->queryParameters(),
				'payload' => $request->payload()
			],
			'data' => $data,
		];

		return true;
	}

	public function verify(): void
	{
		if (! $this->matched)
			throw new Exception('Not found', 404);

		if ($this->handler === null)
			throw new Exception('Route handler missing', 500);

		$this->handler->verify();

		$data = $this->matched['data'];
		$params = $this->matched['params'];
		$expectedInput = $data->expectedInput();

		if (! Payload::verify($params['payload'], $expectedInput['payload']))
			throw new Exception('Provided payload does not match expected', 400);

		if (! Parameter::verifyQuery($params['query'], $expectedInput['query']))
			throw new Exception('Provided query parameters does not match expected', 400);
	}

	public function execute(): void
	{
		$this->handler->call($this->matched['params']);
	}
}

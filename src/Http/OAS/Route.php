<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Exception;
use Projom\Http\Method;
use Projom\Http\Route\Handler;
use Projom\Http\RouteBase;

class Route extends RouteBase
{
	public function __construct(string $path)
	{
		$this->path = $path;
	}

	public function setData(Method $method, Data $data): void
	{
		$this->methodData[$method->name] = $data;
	}

	public function setup(): void
	{
		$data = $this->matched['data'];
		$controller = $data->controllerDetails['controller'];
		$controllerMethod = $data->controllerDetails['method'];
		$this->handler = Handler::create($controller, $controllerMethod);
	}

	protected function verifyData(): void
	{
		$data = $this->matched['data'];
		$params = $this->matched['params'];

		$normalizedPathParams = Parameter::normalize($data->expectedParameters['path'] ?? []);
		if (! Parameter::verifyPath($params['path'], $normalizedPathParams))
			throw new Exception('Provided path parameters does not match expected', 400);

		$normalizedQueryParams = Parameter::normalize($data->expectedParameters['query'] ?? []);
		if (! Parameter::verifyQuery($params['query'], $normalizedQueryParams))
			throw new Exception('Provided query parameters does not match expected', 400);

		if (! Payload::verify($params['payload'], $data->expectedPayload ?? []))
			throw new Exception('Provided payload does not match expected', 400);
	}
}

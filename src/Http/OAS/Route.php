<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Exception;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Route\Handler;
use Projom\Http\Route\Path;
use Projom\Http\Route\RouteBase;

class Route extends RouteBase
{
	public function __construct(string $path)
	{
		$this->path = Path::create($path);
	}

	public function setData(Method $method, Data $data): void
	{
		$this->methodData[$method->name] = $data;
	}

	public function setup(): void
	{
		$controller = $this->matchedData->controllerDetails['controller'];
		$controllerMethod = $this->matchedData->controllerDetails['method'];
		$this->handler = Handler::create($controller, $controllerMethod);
	}

	protected function verifyData(Request $request): void
	{
		$normalizedPathParams = Parameter::normalize($this->matchedData->expectedParameters['path'] ?? []);
		if (! Parameter::verifyPath($request->pathParameters(), $normalizedPathParams))
			throw new Exception('Provided path parameters does not match expected', 400);

		$normalizedQueryParams = Parameter::normalize($this->matchedData->expectedParameters['query'] ?? []);
		if (! Parameter::verifyQuery($request->queryParameters(), $normalizedQueryParams))
			throw new Exception('Provided query parameters does not match expected', 400);

		if (! Payload::verify($request->payload(), $this->matchedData->expectedPayload ?? []))
			throw new Exception('Provided payload does not match expected', 400);
	}
}

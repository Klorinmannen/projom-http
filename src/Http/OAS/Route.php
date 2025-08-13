<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Route\Action;
use Projom\Http\Route\RouteBase;

class Route extends RouteBase
{
	public function setData(Method $method, Data $data): void
	{
		$this->methodData[$method->name] = $data;
	}

	public function setup(): void
	{
		$controller = $this->matchedData->controllerDetails['controller'];
		$controllerMethod = $this->matchedData->controllerDetails['method'];
		$this->action = Action::create($controller, $controllerMethod);
	}

	protected function verifyData(Request $request): void
	{
		if (!Payload::verify($request->payload(), $this->matchedData->expectedPayload ?? []))
			Response::reject('Provided payload does not match expected');

		$normalizedPathParams = Parameter::normalize($this->matchedData->expectedParameters['path'] ?? []);
		if (!Parameter::verifyPath($request->pathParameters(), $normalizedPathParams))
			Response::reject('Provided path parameters do not match expected');

		$normalizedQueryParams = Parameter::normalize($this->matchedData->expectedParameters['query'] ?? []);
		if (!Parameter::verifyExpectedParameters($request->queryParameters(), $normalizedQueryParams))
			Response::reject('Provided query parameters do not match expected');
	}
}

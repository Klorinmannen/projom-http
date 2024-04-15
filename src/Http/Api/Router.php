<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Exception;

use Projom\Http\Response;
use Projom\Http\Request;
use Projom\Http\Api\PathContractInterface;

class Router
{
	public static function start(Request $request, ContractInterface $contract): void
	{
		if (!$pathContract = $contract->match($request))
			throw new Exception('Not found', 404);

		if (!$pathContract->verifyController(ControllerBase::class))
			throw new Exception('Not found', 404);

		if (!$pathContract->verifyInputPathParameters($request->pathParameterList()))
			throw new Exception('Provided path parameters are not valid', 400);

		if (!$pathContract->verifyInputQueryParameters($request->queryParameterList()))
			throw new Exception('Provided query parameters are not valid', 400);

		if (!$pathContract->verifyInputPayload($request->payload()))
			throw new Exception('Provided payload is not valid', 400);

		$response = static::dispatch($request, $pathContract);

		if (!$pathContract->verifyResponse($response->statusCode(), $response->contentType()))
			throw new Exception('Provided response is not valid', 500);

		$response->send();
	}

	public static function dispatch(Request $request, PathContractInterface $routeContract): Response
	{
		$inputs = [
			$request->pathParameterList(),
			$request->queryParameterList(),
			$request->payload()
		];

		$controller = $routeContract->controller();
		$operation = $routeContract->operation();

		$controller = new $controller();
		$controller->{$operation}(...$inputs);

		return $controller->response();
	}
}

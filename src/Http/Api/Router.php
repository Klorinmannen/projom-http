<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Api\PathContractInterface;
use Projom\Http\Response;
use Projom\Http\Request;

class Router
{
	public static function start(Request $request, ContractInterface $contract): void
	{
		if (!$pathContract = $contract->match($request))
			throw new \Exception('Not found', 404);

		if (!$pathContract->verifyInputPathParameters($request->pathParameterList()))
			throw new \Exception('Provided path parameters are not valid', 400);

		if (!$pathContract->verifyInputQueryParameters($request->queryParameters()))
			throw new \Exception('Provided query parameters are not valid', 400);

		if (!$pathContract->verifyInputPayload($request->payload()))
			throw new \Exception('Provided payload is not valid', 400);

		$response = static::dispatch($request, $pathContract);

		if (!$pathContract->verifyResponse($response->statusCode(), $response->contentType()))
			throw new \Exception('Provided response is not valid', 500);

		$response->send();
	}

	public static function dispatch(Request $request, PathContractInterface $pathContract): Response
	{
		$pathController = $pathContract->controller();
		$controller = new $pathController($request->pathParameterList(), $request->queryParameters(), $request->payload());

		$operation = $pathContract->operation();
		$controller->{$operation}();

		return $controller->response();
	}
}

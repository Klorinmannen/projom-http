<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Exception;

use Projom\Http\Response;
use Projom\Http\Request;
use Projom\Http\Api\RouteContractInterface;

class Router
{
	public static function start(
		Request $request,
		ContractInterface $contract
	): void {

		if (!$routeContract = $contract->match($request))
			throw new Exception('Not Found', 404);

		static::processRouteContract($request, $routeContract);
	}

	public static function processRouteContract(
		Request $request,
		RouteContractInterface $routeContract
	): void {

		if (!$routeContract->verifyInputData($request))
			throw new Exception('Bad Request', 400);

		if (!$routeContract->verifyController(ControllerBase::class))
			throw new Exception('Not Implemented', 501);

		$response = static::dispatch($request, $routeContract);

		if (!$routeContract->verifyResponse($response))
			throw new Exception('Internal Server Error', 500);

		$response->send();
	}

	public static function dispatch(
		Request $request,
		RouteContractInterface $routeContract
	): Response {

		$input = [];
		if ($pathParameters = $request->pathParameterList())
			$input[] = $pathParameters;
		if ($queryParameters = $request->queryParameterList())
			$input[] = $queryParameters;
		if ($payload = $request->payload())
			$input[] = $payload;

		$controller = $routeContract->controller();
		$operation = $routeContract->operation();

		$controller = new $controller();
		$controller->{$operation}(...$input);

		return $controller->response();
	}
}

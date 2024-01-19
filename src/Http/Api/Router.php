<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Auth\Service as AuthService;
use Projom\Auth\Jwt;
use Projom\Http\Response;
use Projom\Http\Request;
use Projom\Http\Api\RouteContractInterface;

use Projom\System\SystemException;

class Router
{
	private AuthService $authService;

	public function __construct(AuthService $authService)
	{
		$this->authService = $authService;
	}

	public function start(
		Request $request,
		ContractInterface $contract
	): void {

		if (!$routeContract = $contract->match($request))
			throw new SystemException(404);

		$this->processRouteContract($request, $routeContract);
	}

	public function processRouteContract(
		Request $request,
		RouteContractInterface $routeContract
	): void {

		if (!$routeContract->verifyInputData($request))
			throw new SystemException(400);

		if (!$routeContract->verifyController(ControllerBase::class))
			throw new SystemException(501);

		$response = $this->dispatch($request, $routeContract);

		if (!$routeContract->verifyResponse($response))
			throw new SystemException(500);

		$response->send();
	}

	public function dispatch(
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
		$jwt = new Jwt($request->authToken());

		$authorized = $this->authService->authorize($routeContract->hasAuth(), $controller, $operation, $jwt);
		if ($authorized === false) 
			throw new SystemException(401);

		$controller = new $controller();
		$controller->{$operation}(...$input);

		return $controller->response();
	}
}

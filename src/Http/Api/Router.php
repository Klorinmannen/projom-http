<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Exception;

use Projom\Http\Auth\Jwt;
use Projom\Http\Response;
use Projom\Http\Request;
use Projom\Http\Api\RouteContractInterface;
use Projom\Http\Auth\Jwt\Service;
use Projom\Util\File as UtilFile;

class Router
{
	private Service $jwtService;
	
	public function __construct(Service $jwtService)
	{
		$this->jwtService = $jwtService;
	}

	public static function create(string $JWTClaimsFilePath): Router
	{
		$JWTClaims = UtilFile::parse($JWTClaimsFilePath);
		$JWTService = new Service($JWTClaims);
		return new Router($JWTService);
	}

	public function start(
		Request $request,
		ContractInterface $contract
	): void {

		if (!$routeContract = $contract->match($request))
			throw new Exception('Not Found', 404);

		$this->processRouteContract($request, $routeContract);
	}

	public function processRouteContract(
		Request $request,
		RouteContractInterface $routeContract
	): void {

		if (!$routeContract->verifyInputData($request))
			throw new Exception('Bad Request', 400);

		if (!$routeContract->verifyController(ControllerBase::class))
			throw new Exception('Not Implemented', 501);

		$jwt = new Jwt($request->authToken());
		if ($routeContract->hasAuth())
			if (!$this->jwtService->verify($jwt))
				throw new Exception('Unauthorized', 401);

		$response = $this->dispatch($jwt, $request, $routeContract);

		if (!$routeContract->verifyResponse($response))
			throw new Exception('Internal Server Error', 500);

		$response->send();
	}

	public function dispatch(
		Jwt $jwt,
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
		$controller->setJwt($jwt);
		$controller->{$operation}(...$input);

		return $controller->response();
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response as httpResponse;
use Projom\Http\OAS\Response;

class Data
{
	public array $requiredParameters = [];
	public array $requiredPayload = [];
	public array $requiredResponse = [];
	public bool $security = false;
	public array $controllerDetails = [];
	public Method $method;

	public function __construct() {}

	public static function create(Method $method, array $routeData): Data
	{
		$data = new Data();
		$data->method = $method;
		$data->requiredParameters = Parameter::normalize($routeData['parameters'] ?? []);
		$data->requiredPayload = Payload::normalize($routeData['requestBody'] ?? []);
		$data->requiredResponse = Response::normalize($routeData['responses'] ?? []);
		$data->security = Security::normalize($routeData['security'] ?? []);
		$data->controllerDetails = Controller::normalize($routeData['operationId'] ?? '');
		return $data;
	}

	public function verify(Request $request): void
	{
		if (!Payload::verify($request->payload(), $this->requiredPayload ?? []))
			httpResponse::reject('Provided payload does not match expected');

		$normalizedPathParams = Parameter::normalize($this->requiredParameters['path'] ?? []);
		if (!Parameter::verifyPath($request->pathParameters(), $normalizedPathParams))
			httpResponse::reject('Provided path parameters do not match expected');

		$normalizedQueryParams = Parameter::normalize($this->requiredParameters['query'] ?? []);
		if (!Parameter::verifyRequired($request->queryParameters(), $normalizedQueryParams))
			httpResponse::reject('Provided query parameters do not match expected');
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Method;

class Data
{
	public array $expectedParameters = [];
	public array $expectedPayload = [];
	public array $expectedResponse = [];
	public bool $security = false;
	public array $controllerDetails = [];
	public Method $method;

	public function __construct() {}

	public static function create(Method $method, array $routeData): Data
	{
		$data = new Data();
		$data->method = $method;
		$data->expectedParameters = Parameter::normalize($routeData['parameters'] ?? []);
		$data->expectedPayload = Payload::normalize($routeData['requestBody'] ?? []);
		$data->expectedResponse = Response::normalize($routeData['responses'] ?? []);
		$data->security = Security::normalize($routeData['security'] ?? []);
		$data->controllerDetails = Controller::normalize($routeData['operationId'] ?? '');
		return $data;
	}
}

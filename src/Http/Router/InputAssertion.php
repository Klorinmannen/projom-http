<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Router\InputAssertionInterface;
use Projom\Http\Router\InputAssertion\Parameter;
use Projom\Http\Router\InputAssertion\Payload;
use Projom\Http\Router\Route\RouteBase;

class InputAssertion implements InputAssertionInterface
{
	public function __construct() {}

	public function verify(Request $request, RouteBase $route): void
	{
		$route->isComplete();
		$data = $route->matchedData();

		// 1. Check mandatory definitions.
		$normalizedMandatoryQueryParams = Parameter::normalize($data->mandatoryQueryParamDefinitions);
		if (!Parameter::verifyMandatory($request->queryParameters(), $normalizedMandatoryQueryParams))
			Response::reject('Mandatory query parameters do not match provided definitions');

		$normalizedMandatoryRequestVars = Parameter::normalize($data->mandatoryRequestVarDefinitions);
		if (!Parameter::verifyMandatory($request->vars(), $normalizedMandatoryRequestVars))
			Response::reject('Mandatory request variables do not match provided definitions');

		// 2. Check required definitions.
		$normalizedRequiredQueryParams = Parameter::normalize($data->requiredQueryParamDefinitions);
		if (!Parameter::verifyRequired($request->queryParameters(), $normalizedRequiredQueryParams))
			Response::reject('Required request query parameters do not match provided definitions');

		$normalizedRequiredRequestVars = Parameter::normalize($data->requiredRequestVarDefinitions);
		if (!Parameter::verifyRequired($request->vars(), $normalizedRequiredRequestVars))
			Response::reject('Required request variables do not match provided definitions');

		// 3. Check optional definitions.
		$normalizedOptionalQueryDefinitions = Parameter::normalize($data->optionalQueryParamDefinitions);
		if (!Parameter::verifyOptional($request->queryParameters(), $normalizedOptionalQueryDefinitions))
			Response::reject('Optional query parameters do not match provided definitions');

		$normalizedOptionalRequestVarDefinitions = Parameter::normalize($data->optionalRequestVarDefinitions);
		if (! Parameter::verifyOptional($request->vars(), $normalizedOptionalRequestVarDefinitions))
			Response::reject('Optional request variables do not match provided definitions');

		// 4. Check payload if required.
		if (!Payload::verify($request->payload(), $data->requiredPayload))
			Response::reject('Required request payload missing');
	}
}

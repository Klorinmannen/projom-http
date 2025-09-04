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
		$inputDefinition = $route->inputDefinition();

		// 1. Check mandatory definitions.
		$normalizedMandatoryQueryParams = Parameter::normalize($inputDefinition->mandatoryQueryParamDefinitions);
		if (!Parameter::verifyMandatory($request->queryParameters(), $normalizedMandatoryQueryParams))
			Response::reject('Mandatory query parameters do not match provided definitions');

		$normalizedMandatoryRequestVars = Parameter::normalize($inputDefinition->mandatoryRequestVarDefinitions);
		if (!Parameter::verifyMandatory($request->vars(), $normalizedMandatoryRequestVars))
			Response::reject('Mandatory request variables do not match provided definitions');

		// 2. Check required definitions.
		$normalizedRequiredQueryParams = Parameter::normalize($inputDefinition->requiredQueryParamDefinitions);
		if (!Parameter::verifyRequired($request->queryParameters(), $normalizedRequiredQueryParams))
			Response::reject('Required request query parameters do not match provided definitions');

		$normalizedRequiredRequestVars = Parameter::normalize($inputDefinition->requiredRequestVarDefinitions);
		if (!Parameter::verifyRequired($request->vars(), $normalizedRequiredRequestVars))
			Response::reject('Required request variables do not match provided definitions');

		// 3. Check optional definitions.
		$normalizedOptionalQueryDefinitions = Parameter::normalize($inputDefinition->optionalQueryParamDefinitions);
		if (!Parameter::verifyOptional($request->queryParameters(), $normalizedOptionalQueryDefinitions))
			Response::reject('Optional query parameters do not match provided definitions');

		$normalizedOptionalRequestVarDefinitions = Parameter::normalize($inputDefinition->optionalRequestVarDefinitions);
		if (! Parameter::verifyOptional($request->vars(), $normalizedOptionalRequestVarDefinitions))
			Response::reject('Optional request variables do not match provided definitions');

		// 4. Check payload if required.
		if (!Payload::verify($request->payload(), $inputDefinition->requiredPayload))
			Response::reject('Required request payload missing');
	}
}

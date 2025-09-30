<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input;

use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Router\Input\AssertionInterface;
use Projom\Http\Router\Input\Assertion\Parameter\Mandatory;
use Projom\Http\Router\Input\Assertion\Parameter\Optional;
use Projom\Http\Router\Input\Assertion\Parameter\Required;
use Projom\Http\Router\Input\Assertion\Payload;
use Projom\Http\Router\Input\Assertion\Util;
use Projom\Http\Router\Route\Input\Definition;

class Assertion implements AssertionInterface
{
	public function __construct() {}

	public function verify(Request $request, Definition $inputDefinition): void
	{
		// 1. Check mandatory definitions.
		$normalizedMandatoryQueryParams = Util::normalize($inputDefinition->mandatoryQueryParamDefinitions);
		if (!Mandatory::verify($request->queryParameters(), $normalizedMandatoryQueryParams))
			Response::reject('Mandatory query parameters are missing');

		$normalizedMandatoryRequestVars = Util::normalize($inputDefinition->mandatoryRequestVarDefinitions);
		if (!Mandatory::verify($request->vars(), $normalizedMandatoryRequestVars))
			Response::reject('Mandatory request variables are missing');

		// 2. Check required definitions.
		$normalizedRequiredQueryParams = Util::normalize($inputDefinition->requiredQueryParamDefinitions);
		if (!Required::verify($request->queryParameters(), $normalizedRequiredQueryParams))
			Response::reject('Required query parameters are missing');

		$normalizedRequiredRequestVars = Util::normalize($inputDefinition->requiredRequestVarDefinitions);
		if (!Required::verify($request->vars(), $normalizedRequiredRequestVars))
			Response::reject('Required request variables are missing');

		// 3. Check optional definitions.
		$normalizedOptionalQueryDefinitions = Util::normalize($inputDefinition->optionalQueryParamDefinitions);
		if (!Optional::verify($request->queryParameters(), $normalizedOptionalQueryDefinitions))
			Response::reject('Optional query parameters do not match provided definitions');

		$normalizedOptionalRequestVarDefinitions = Util::normalize($inputDefinition->optionalRequestVarDefinitions);
		if (!Optional::verify($request->vars(), $normalizedOptionalRequestVarDefinitions))
			Response::reject('Optional request variables do not match provided definitions');

		// 4. Check payload if required.
		if (!Payload::verify($request->payload(), $inputDefinition->requiredPayload))
			Response::reject('Required payload is missing');
	}
}

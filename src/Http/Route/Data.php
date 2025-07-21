<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Route\DataInterface;

class Data implements DataInterface
{
	private bool $expectsPayload = false;
	private array $expectsQueryParamDefinitions = [];
	private array $optionalQueryParamDefinitions = [];
	private array $expectsRequestVarDefinitions = [];
	private array $optionalRequestVarDefinitions = [];

	public function __construct(
		private Method $method,
		private string $controllerMethod
	) {}

	public static function create(Method $method, string $controllerMethod = ''): Data
	{
		return new Data($method, $controllerMethod);
	}

	public function method(): string
	{
		if ($this->hasControllerMethod())
			return $this->controllerMethod;
		return $this->method->value;
	}

	private function hasControllerMethod(): bool
	{
		return $this->controllerMethod !== '';
	}

	public function expectsPayload(bool $expectsPayload = true): Data
	{
		$this->expectsPayload = $expectsPayload;
		return $this;
	}

	public function expectsQueryParameters(array $queryParameterDefinitions): Data
	{
		$this->expectsQueryParamDefinitions = $queryParameterDefinitions;
		return $this;
	}

	public function optionalQueryParameters(array $queryParameterDefinitions): Data
	{
		$this->optionalQueryParamDefinitions = $queryParameterDefinitions;
		return $this;
	}

	public function expectsRequestVars(array $requestVarDefinitions): Data
	{
		$this->expectsRequestVarDefinitions = $requestVarDefinitions;
		return $this;
	}

	public function optionalRequestVars(array $requestVarDefinitions): Data
	{
		$this->optionalRequestVarDefinitions = $requestVarDefinitions;
		return $this;
	}

	public function verify(Request $request): void
	{
		// Check expected data first.

		if (!Payload::verify($request->payload(), $this->expectsPayload))
			Response::reject('Provided payload does not match expected');

		$normalizedQueryParams = Parameter::normalize($this->expectsQueryParamDefinitions);
		if (!Parameter::verifyExpectedParameters($request->queryParameters(), $normalizedQueryParams))
			Response::reject('Provided query parameters do not match expected');

		$normalizedRequestVars = Parameter::normalize($this->expectsRequestVarDefinitions);
		if (!Parameter::verifyExpectedParameters($request->vars(), $normalizedRequestVars))
			Response::reject('Provided request variables do not match expected');

		// Check optional data next.

		$normalizedOptionalQueryDefinitions = Parameter::normalize($this->optionalQueryParamDefinitions);
		if (!Parameter::verifyOptionalParameters($request->queryParameters(), $normalizedOptionalQueryDefinitions))
			Response::reject('Provided query parameters does not match optional');

		$normalizedOptionalRequestVarDefinitions = Parameter::normalize($this->optionalRequestVarDefinitions);
		if (! Parameter::verifyOptionalParameters($request->vars(), $normalizedOptionalRequestVarDefinitions))
			Response::reject('Provided request variables does not match optional');
	}
}

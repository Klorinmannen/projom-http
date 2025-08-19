<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Router\Route\DataInterface;
use Projom\Http\Router\Route\Parameter;

class Data implements DataInterface
{
	private bool $requiredPayload = false;

	private array $mandatoryQueryParamDefinitions = [];
	private array $requiredQueryParamDefinitions = [];
	private array $optionalQueryParamDefinitions = [];

	private array $mandatoryRequestVarDefinitions = [];
	private array $requiredRequestVarDefinitions = [];
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

	/**
	 * Set required payload.
	 * The payload is required to be present in the request.
	 */
	public function requiredPayload(): Data
	{
		$this->requiredPayload = true;
		return $this;
	}

	/**
	 * Set mandatory query parameters.
	 * Mandatory query parameters are exclusive, meaning that if they are set, no other query parameters are allowed.
	 * All mandatory query parameters must be present in the request.
	 * @param array [ 'id,page_id' => 'integer', 'name' => 'string', ... ]
	 */
	public function mandatoryQueryParameters(array $definitions): void
	{
		$this->mandatoryQueryParamDefinitions = $this->parseDefinitions($definitions);
		$this->requiredQueryParamDefinitions = [];
		$this->optionalQueryParamDefinitions = [];
	}

	/**
	 * Set required query parameters.
	 * Required query parameters are not exclusive, meaning that other query parameters can be present in the request.
	 * All required query parameters must be present in the request.
	 * @param array [ 'id' => 'integer', 'name' => 'string', ... ]
	 */
	public function requiredQueryParameters(array $definitions): Data
	{
		$this->requiredQueryParamDefinitions = $this->parseDefinitions($definitions);
		return $this;
	}

	/**
	 * Set optional query parameters.
	 * Optional query parameters are not exclusive, meaning that other query parameters can be present in the request.
	 * Optional query parameters are not required to be present in the request.
	 * @param array [ 'id' => 'integer', 'name' => 'string', ... ]
	 */
	public function optionalQueryParameters(array $definitions): Data
	{
		$this->optionalQueryParamDefinitions = $this->parseDefinitions($definitions);
		return $this;
	}

	/**
	 * Set mandatory request variables.
	 * Mandatory request variables are exclusive, meaning that if they are set, no other request variables are allowed.
	 * All mandatory request variables must be present in the request.
	 * @param array [ 'id' => 'integer', 'name' => 'string', ... ]
	 */
	public function mandatoryRequestVars(array $definitions): void
	{
		$this->mandatoryRequestVarDefinitions = $this->parseDefinitions($definitions);
		$this->requiredRequestVarDefinitions = [];
		$this->optionalRequestVarDefinitions = [];
	}

	/**
	 * Set required request variables.
	 * Required request variables are not exclusive, meaning that other request variables can be present in the request.
	 * All required request variables must be present in the request.
	 * @param array [ 'id' => 'integer', 'name' => 'string', ... ] 
	 */
	public function requiredRequestVars(array $definitions): Data
	{
		$this->requiredRequestVarDefinitions = $this->parseDefinitions($definitions);
		return $this;
	}

	/**
	 * Set optional request variables.
	 * Optional request variables are not exclusive, meaning that other request variables can be present in the request.
	 * Optional request variables are not required to be present in the request.
	 * @param array [ 'id' => 'integer', 'name' => 'string', ... ] 
	 */
	public function optionalRequestVars(array $definitions): Data
	{
		$this->optionalRequestVarDefinitions = $this->parseDefinitions($definitions);
		return $this;
	}

	private function parseDefinitions(array $definitions): array
	{
		$parsedDefinitions = [];
		foreach ($definitions as $nameString => $parameterType) {
			$names = $this->splitNameString($nameString);
			foreach ($names as $name)
				$parsedDefinitions[$name] = $parameterType;
		}
		return $parsedDefinitions;
	}

	private function splitNameString(string $nameString): array
	{
		$nameString = trim($nameString);
		$nameString = str_replace(' ', '', $nameString);
		$names = explode(',', $nameString);
		return $names;
	}

	/**
	 * Verify the request against set definitions.
	 * 1. Check mandatory definitions.
	 * 2. Check required definitions.
	 * 3. Check optional definitions.
	 * 4. Check if payload is required.
	 */
	public function verify(Request $request): void
	{
		// 1. Check mandatory definitions.
		$normalizedMandatoryQueryParams = Parameter::normalize($this->mandatoryQueryParamDefinitions);
		if (!Parameter::verifyMandatory($request->queryParameters(), $normalizedMandatoryQueryParams))
			Response::reject('Mandatory query parameters do not match provided definitions');

		$normalizedMandatoryRequestVars = Parameter::normalize($this->mandatoryRequestVarDefinitions);
		if (!Parameter::verifyMandatory($request->vars(), $normalizedMandatoryRequestVars))
			Response::reject('Mandatory request variables do not match provided definitions');

		// 2. Check required definitions.
		$normalizedRequiredQueryParams = Parameter::normalize($this->requiredQueryParamDefinitions);
		if (!Parameter::verifyRequired($request->queryParameters(), $normalizedRequiredQueryParams))
			Response::reject('Required request query parameters do not match provided definitions');

		$normalizedRequiredRequestVars = Parameter::normalize($this->requiredRequestVarDefinitions);
		if (!Parameter::verifyRequired($request->vars(), $normalizedRequiredRequestVars))
			Response::reject('Required request variables do not match provided definitions');

		// 3. Check optional definitions.
		$normalizedOptionalQueryDefinitions = Parameter::normalize($this->optionalQueryParamDefinitions);
		if (!Parameter::verifyOptional($request->queryParameters(), $normalizedOptionalQueryDefinitions))
			Response::reject('Optional query parameters do not match provided definitions');

		$normalizedOptionalRequestVarDefinitions = Parameter::normalize($this->optionalRequestVarDefinitions);
		if (! Parameter::verifyOptional($request->vars(), $normalizedOptionalRequestVarDefinitions))
			Response::reject('Optional request variables do not match provided definitions');

		// 4. Check payload if required.
		if (!Payload::verify($request->payload(), $this->requiredPayload))
			Response::reject('Required request payload missing');
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route\Input;

use Projom\Http\Method;
use Projom\Http\Router\Route\Input\DefinitionInterface;

/**
 * Data Transfer Object for route input definitions.
 */
class Definition implements DefinitionInterface
{
	protected Method $method = Method::GET;
	protected string $controllerMethod = '';

	public bool $requiredPayload = false;
	public array $mandatoryQueryParamDefinitions = [];
	public array $requiredQueryParamDefinitions = [];
	public array $optionalQueryParamDefinitions = [];
	public array $mandatoryRequestVarDefinitions = [];
	public array $requiredRequestVarDefinitions = [];
	public array $optionalRequestVarDefinitions = [];

	public function __construct(
		Method $method,
		string $controllerMethod
	) {
		$this->method = $method;
		$this->controllerMethod = $controllerMethod;
	}

	public static function create(Method $method, string $controllerMethod = ''): Definition
	{
		return new Definition($method, $controllerMethod);
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
	public function requiredPayload(): DefinitionInterface
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
	public function requiredQueryParameters(array $definitions): DefinitionInterface
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
	public function optionalQueryParameters(array $definitions): DefinitionInterface
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
	public function requiredRequestVars(array $definitions): DefinitionInterface
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
	public function optionalRequestVars(array $definitions): DefinitionInterface
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
}

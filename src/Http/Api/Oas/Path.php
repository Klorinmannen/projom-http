<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Api\Oas\ParameterContract;
use Projom\Http\Api\Oas\PayloadContract;
use Projom\Http\Api\Oas\ResponseContract;

class Path
{
	private readonly ParameterContract $parameterContract;
	private readonly PayloadContract $payloadContract;
	private readonly ResponseContract $responseContract;
	private readonly string $resourceOperation;
	private readonly string $resourceController;
	private readonly bool $auth;

	public function __construct(array $pathDetails)
	{
		$parameterContracts = $pathDetails['parameters'] ?? [];
		$this->parameterContract = new ParameterContract($parameterContracts);

		$payloadContracts = $pathDetails['requestBody'] ?? [];
		$this->payloadContract = new PayloadContract($payloadContracts);

		$responseContracts = $pathDetails['responses'] ?? [];
		$this->responseContract = new ResponseContract($responseContracts);

		$security = $pathDetails['security'] ?? [];
		$this->auth = $security ? true : false;

		[$resourceController, $resourceOperation] = $this->formatResourceOperation($pathDetails['operationId'] ?? '');

		$this->resourceController = $resourceController;
		$this->resourceOperation = $resourceOperation;
	}

	public static function create(array $pathDetails): Path
	{
		return new Path($pathDetails);
	}

	public function formatResourceOperation(string $operationId): array
	{
		$operationDetails = explode('@', $operationId);

		$resourceController = array_shift($operationDetails) ?? '';
		$resourceController = ucwords(str_replace('_', '\\',  $resourceController), '\\');

		$resourceOperation = array_shift($operationDetails) ?? '';

		return [$resourceController, $resourceOperation];
	}

	public function parameterContract(): ParameterContract
	{
		return $this->parameterContract;
	}

	public function payloadContract(): PayloadContract
	{
		return $this->payloadContract;
	}

	public function responseContract(): ResponseContract
	{
		return $this->responseContract;
	}

	public function resourceOperation(): string
	{
		return $this->resourceOperation;
	}

	public function resourceController(): string
	{
		return $this->resourceController;
	}

	public function hasAuth(): bool
	{
		return $this->auth;
	}
}

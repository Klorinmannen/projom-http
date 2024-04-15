<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Api\Oas\ParameterContract;
use Projom\Http\Api\Oas\PayloadContract;
use Projom\Http\Api\Oas\ResponseContract;

class Path
{
	private ParameterContract|null $parameterContract = null;
	private PayloadContract|null $payloadContract = null;
	private ResponseContract|null $responseContract = null;

	private string $operation = '';
	private string $resourceController = '';
	private bool $auth = true;

	public function __construct(array $pathDetails)
	{
		$parameterContracts = $pathDetails['parameters'] ?? [];
		$this->parameterContract = new ParameterContract($parameterContracts);

		$payloadContracts = $pathDetails['requestBody'] ?? [];
		$this->payloadContract = new PayloadContract($payloadContracts);

		$responseContracts = $pathDetails['responses'] ?? [];
		$this->responseContract = new ResponseContract($responseContracts);

		if (array_key_exists('security', $pathDetails))
			$this->auth = $pathDetails['security'] ? true : false;

		$operaionDetails = explode('@', $pathDetails['operationId'] ?? '');
		$this->resourceController = ucwords(str_replace('_', '\\', array_shift($operaionDetails) ?? ''), '\\');
		$this->operation = array_shift($operaionDetails) ?? '';
	}

	public static function create(array $pathDetails): Path
	{
		return new Path($pathDetails);
	}

	public function parameterContract(): ParameterContract|null
	{
		return $this->parameterContract;
	}

	public function payloadContract(): PayloadContract|null
	{
		return $this->payloadContract;
	}

	public function responseContract(): ResponseContract|null
	{
		return $this->responseContract;
	}

	public function operation(): string
	{
		return $this->operation;
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

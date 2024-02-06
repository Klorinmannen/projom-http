<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Api\Oas\ParameterContract;
use Projom\Http\Api\Oas\PayloadContract;
use Projom\Http\Api\Oas\ResponseContract;
use Projom\Http\Response;

class PathContract
{
	private ParameterContract $parameterContract;
	private PayloadContract $payloadContract;
	private ResponseContract $responseContract;
	private string $operation = '';
	private bool $auth = true;

	public function __construct(array $pathContract = [])
	{
		$parameterContracts = $pathContract['parameters'] ?? [];
		$this->parameterContract = new ParameterContract($parameterContracts);

		$payloadContracts = $pathContract['requestBody'] ?? [];
		$this->payloadContract = new PayloadContract($payloadContracts);

		$responseContracts = $pathContract['responses'] ?? [];
		$this->responseContract = new ResponseContract($responseContracts);

		if (array_key_exists('security', $pathContract))
			$this->auth = $pathContract['security'] ? true : false;

		$this->operation = $pathContract['operationId'] ?? '';
	}

	public function verifyPathParameters(array $inputParameters): bool
	{
		return $this->parameterContract->verifyPath($inputParameters);
	}

	public function verifyQueryParameters(array $inputParameters): bool
	{
		return $this->parameterContract->verifyQuery($inputParameters);
	}

	public function verifyPayload(string $inputPayload): bool
	{
		return $this->payloadContract->verify($inputPayload);
	}

	public function verifyResponse(Response $response): bool
	{
		return $this->responseContract->verify(
			$response->statusCode(),
			$response->contentType()
		);
	}

	public function operation(): string
	{
		return $this->operation;
	}

	public function hasAuth(): bool
	{
		return $this->auth;
	}
}

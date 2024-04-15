<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Contracts;
use Projom\Http\Request;
use Projom\Http\Api\Router;
use Projom\Http\Api\Oas\Contract;

class Api
{
	private static ContractInterface|null $contract = null;

	public static function loadContract(array $config): void
	{
		$contract_type = Contracts::tryFrom($config['contract_type'] ?? '');
		static::$contract = match ($contract_type) {
			Contracts::OAS => Contract::create($config['contract_file_path']),
			default => throw new \Exception("Contract {$contract_type} not supported", 400),
		};
	}

	public static function dispatch(Request $request): void
	{
		if (!static::$contract)
			throw new \Exception('Contract not loaded', 400);

		Router::start($request, static::$contract);
	}
}

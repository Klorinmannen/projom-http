<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Contract;
use Projom\Http\Request;
use Projom\Http\Api\Router;
use Projom\Http\Api\Oas\Contract as OasContract;

class Api
{
	private static null|ContractInterface $contract = null;

	public static function loadContract(array $config): void
	{
		$contract_type = Contract::tryFrom($config['contract_type'] ?? '');
		static::$contract = match ($contract_type) {
			Contract::OAS => OasContract::create($config['contract_file_path']),
			default => throw new \Exception("Contract $contract_type, is not supported", 400),
		};
	}

	public static function dispatch(Request $request): void
	{
		if (static::$contract === null)
			throw new \Exception('Contract not loaded', 400);

		Router::start($request, static::$contract);
	}
}

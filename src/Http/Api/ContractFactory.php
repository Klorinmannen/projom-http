<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Oas\Contract;
use Projom\Http\Api\Oas\Repository;
use Projom\System\SystemException;

class ContractFactory
{
	const CONTRACT_TYPE_OAS = 'OAS';

	public static function create(array $contractConfig): ContractInterface
	{
		$type = $contractConfig['type'] ?? '';
		$path = $contractConfig['path'] ?? '';

		$contract = match ($type) {
			static::CONTRACT_TYPE_OAS => static::createOas($path),
			default => null
		};

		if ($contract === null)
			throw new SystemException(500, 'ContractFactory returns null');

		return $contract;
	}

	public static function createOas(string $path): ContractInterface
	{
		$repository = new Repository($path);
		return new Contract($repository);
	}
}
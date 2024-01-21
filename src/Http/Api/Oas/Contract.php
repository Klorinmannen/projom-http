<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Request;
use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Api\Oas\RouteContract;
use Projom\Http\Api\Oas\File;
use Projom\Http\Api\RouteContractInterface;

class Contract implements ContractInterface
{
	private array $contracts = [];
	private File $file;

	public function __construct(File $file)
	{
		$this->file = $file;
	}

	public static function create(string $apiContractFilePath): Contract
	{
		$file = new File($apiContractFilePath);
		$contract = new Contract($file);
		$contract->load();
		return $contract;
	}

	public function load(): bool
	{
		if (!$contract = $this->file->contract())
			return false;

		$this->contracts = $this->loadContract($contract);

		return true;
	}

	public function loadContract(array $contract): array 
	{
		$routeContracts = [];
		foreach ($contract as $rawPattern => $contractDetails) {

			if (!$routePathContracts = $contractDetails['route_path_contracts'] ?? [])
				continue;

			$pathContracts = [];
			foreach ($routePathContracts as $httpMethod => $pathContract) {
				$httpMethod = strtoupper($httpMethod);
				$pathContracts[$httpMethod] = new PathContract($pathContract);
			}

			$routePattern = $contractDetails['route_pattern'];
			$routeController = $contractDetails['route_controller'];

			$routeContracts[$rawPattern] = new RouteContract(
				$pathContracts,
				$routePattern,
				$routeController
			);
		}

		// Prioritzes paths.
		$routeContracts = $this->sortRouteContracts($routeContracts);

		return $routeContracts;
	}

	public function sortRouteContracts(array $routeContracts): array
	{
		ksort($routeContracts);
		return $routeContracts;
	}

	public function match(Request $request): ?RouteContractInterface
	{
		foreach ($this->contracts as $routeContract)
			if ($routeContract->match($request))
				return $routeContract;

		return null;
	}
}

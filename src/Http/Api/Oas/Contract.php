<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Request;
use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Api\Oas\RouteContract;
use Projom\Http\Api\Oas\Repository;
use Projom\Http\Api\RouteContractInterface;

class Contract implements ContractInterface
{
	private array $contracts = [];
	private Repository $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	public function load(): bool
	{
		if (!$contract = $this->repository->contract())
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

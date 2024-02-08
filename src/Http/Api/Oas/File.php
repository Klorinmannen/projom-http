<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Util\File as UtilFile;
use Projom\Http\Api\Pattern;

class File
{
	private array $contract = [];
	private string $mainContractFilePath = '';

	public function __construct(string $mainContractFilePath)
	{
		$contractFile = $this->parseFile($mainContractFilePath);
		$this->contract = static::parseContract($contractFile);
		$this->mainContractFilePath = $mainContractFilePath;
	}

	public function parseContract(array $contractFile): array
	{
		if (!$contractDetails = $contractFile['paths'] ?? [])
			return [];

		$contractDir = dirname($this->mainContractFilePath);

		$contract = [];
		foreach ($contractDetails as $rawPattern => $contractRef) {

			[$pathContractFilePath, $contractName] = $this->splitContractRef($contractRef);

			$fullPathContractFilePath = $contractDir . '/' . $pathContractFilePath;
			$pathContractFile = $this->parseFile($fullPathContractFilePath);
			$pathContracts = $pathContractFile[$contractName];

			$contract[$rawPattern] = [
				'route_path_contracts' => $pathContracts,
				'route_pattern' => Pattern::create($rawPattern),
				'route_controller' => $this->routeController($pathContractFilePath)
			];
		}

		return $contract;
	}

	public function splitContractRef(array $contractRef): array
	{
		return explode('#/', $contractRef['$ref']);
	}

	public function parseFile(string $fullFilePath): array
	{
		return UtilFile::parse($fullFilePath);
	}

	public function routeController(string $contractFilePath): string
	{
		if (!$contractFilePath)
			return '';

		$contractFilePath = preg_replace('/\/+/', '/', $contractFilePath);
		$contractFilePath = str_replace('/', '\\', $contractFilePath);
		$contractFilePath = str_replace([ '.yml', '.yaml' ], '', $contractFilePath);
		$route = trim($contractFilePath, '\\');

		return '\\' . $route . '\\Controller';
	}

	public function contract(): array
	{
		return $this->contract;
	}
}

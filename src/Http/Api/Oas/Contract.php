<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Request;
use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Oas\Path;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Api\PathContractInterface;
use Projom\Http\Api\Pattern;
use Projom\Util\File;

class Contract implements ContractInterface
{
	private array $contracts = [];
	private array $file = [];

	public function __construct(string $contractFilePath)
	{
		$this->file = File::parse($contractFilePath);
		$this->build();
	}

	public static function create(string $contractFilePath): Contract
	{
		return new Contract($contractFilePath);
	}

	private function build(): void
	{
		if (!$filePaths = $this->file['paths'] ?? [])
			return;

		$contracts = [];
		foreach ($filePaths as $pathPattern => $path) {

			$paths = [];
			foreach ($path as $httpMethod => $pathDetails) {
				$httpMethod = strtoupper($httpMethod);
				$paths[$httpMethod] = Path::create($pathDetails);
			}

			$pattern = Pattern::build($pathPattern);
			$contracts[$pathPattern] = [
				$pattern,
				$paths
			];
		}

		// Prioritzes/sorts paths.
		ksort($contracts);

		$this->contracts = $contracts;
		#var_dump($this->contracts['/users/{id}'][1]);
	}

	public function match(Request $request): PathContractInterface|null
	{
		foreach ($this->contracts as [$pattern, $paths]) {

			if (!$request->matchPattern($pattern))
				continue;

			if (!$path = $paths[$request->httpMethod()] ?? null)
				continue;

			return PathContract::create($path);
		}

		return null;
	}
}

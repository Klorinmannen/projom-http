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
	private readonly array $contracts;

	public function __construct(string $contractFilePath)
	{
		$file = File::parse($contractFilePath);
		$this->build($file);
	}

	public static function create(string $contractFilePath): Contract
	{
		return new Contract($contractFilePath);
	}

	private function build(array $file): void
	{
		$filePaths = $file['paths'] ?? [];

		$contracts = [];
		foreach ($filePaths as $pathPattern => $path) {

			$paths = [];
			foreach ($path as $httpMethod => $pathDetails) {
				$httpMethod = strtoupper($httpMethod);
				$paths[$httpMethod] = Path::create($pathDetails);
			}

			$pattern = Pattern::build($pathPattern);
			$contracts[$pathPattern] = [$pattern, $paths];
		}

		// Prioritze paths.
		ksort($contracts);

		$this->contracts = $contracts;
	}

	public function match(Request $request): null|PathContractInterface
	{
		foreach ($this->contracts as [$pattern, $paths]) {

			if (!$request->matchPattern($pattern))
				continue;

			$path = $paths[$request->httpMethod()] ?? null;
			if ($path === null)
				continue;

			return PathContract::create($path);
		}

		return null;
	}
}

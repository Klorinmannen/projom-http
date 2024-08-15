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
		$this->build($contractFilePath);
	}

	public static function create(string $contractFilePath): Contract
	{
		return new Contract($contractFilePath);
	}

	private function build(string $contractFilePath): void
	{
		$contracts = [];

		$file = File::parse($contractFilePath);
		$filePaths = $file['paths'] ?? [];

		foreach ($filePaths as $pathPattern => $path) {

			$paths = [];
			foreach ($path as $key => $pathDetails) {

				$newPath = [];
				if ($key === '$ref')
					$newPath = $this->buildRefPaths($pathDetails, dirname($contractFilePath));
				else
					$newPath = $this->buildPath($key, $pathDetails);

				$paths = [...$paths, ...$newPath];
			}

			$pattern = Pattern::build($pathPattern);
			$contracts[$pathPattern] = [$pattern, $paths];
		}

		// Prioritze paths.
		ksort($contracts);

		$this->contracts = $contracts;
	}

	private function buildPath(string $httpMethod, array $pathDetails): array
	{
		$httpMethod = strtoupper($httpMethod);
		$path = Path::create($pathDetails);
		return [$httpMethod => $path];
	}

	private function buildRefPaths(string $ref, string $contractDirectory): array
	{
		[$relativeFilename, $refname] = explode('#/', $ref);

		$refFilepath = $contractDirectory . '/' . $relativeFilename;
		$file = File::parse($refFilepath);

		$paths = [];
		$path = $file[$refname] ?? [];
		foreach ($path as $httpMethod => $pathDetails)
			$paths[] = $this->buildPath($httpMethod, $pathDetails);

		return $paths;
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

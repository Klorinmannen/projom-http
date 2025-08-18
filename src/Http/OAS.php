<?php

declare(strict_types=1);

namespace Projom\Http;

use RuntimeException;

use Projom\Http\Method;
use Projom\Http\OAS\Route;
use Projom\Http\OAS\Route\Data;

class OAS
{
	public static function load(string $filePath): array
	{
		$routes = [];
		$file = static::parseFile($filePath);

		$paths = $file['paths'] ?? [];
		if (!$paths)
			throw new RuntimeException("No paths found in OAS file: $filePath");

		foreach ($paths as $path => $pathData) {

			$route = Route::create($path);
			foreach ($pathData as $key => $pathDetails) {
				if ($key === '$ref')
					static::addFromRef($route, $pathDetails, dirname($filePath));
				else
					static::addRouteData($route, $key, $pathDetails);
			}

			$routes[$path] = $route;
		}

		return $routes;
	}

	private static function addRouteData(Route $route, string $httpMethod, array $pathDetails): void
	{
		$method = Method::from(strtoupper($httpMethod));
		$data = Data::create($method, $pathDetails);
		$route->setData($method, $data);
	}

	private static function addFromRef(Route $route, string $ref, string $contractDirectory): void
	{
		[$relativeFilename, $refname] = explode('#/', $ref);
		$refFilepath = $contractDirectory . '/' . $relativeFilename;
		$fileData = static::parseFile($refFilepath);

		$paths = $fileData[$refname] ?? [];
		if (!$paths)
			throw new RuntimeException("Reference not found: $refname in $refFilepath");

		foreach ($paths as $httpMethod => $pathDetails)
			static::addRouteData($route, $httpMethod, $pathDetails);
	}

	private static function parseFile(string $filePath): array
	{
		$filePath = realpath($filePath);
		if (!$filePath)
			throw new RuntimeException("File not found: $filePath");

		$file = file_get_contents($filePath);
		$fileData = static::decodeFile($file, $filePath);
		return $fileData;
	}

	private static function decodeFile(string $file, string $filePath): array
	{
		$extension = pathinfo($filePath, PATHINFO_EXTENSION);
		$fileData = match ($extension) {
			'json' => json_decode($file, associative: true) ?: [],
			'yaml', 'yml' => yaml_parse($file) ?: [],
			default => throw new RuntimeException("Unsupported file format: $extension for file $filePath"),
		};
		return $fileData;
	}
}

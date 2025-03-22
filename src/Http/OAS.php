<?php

declare(strict_types=1);

namespace Projom\Http;

use Exception;

use Projom\Http\OAS\Route as OASRoute;
use Projom\Http\OAS\Data;

class OAS
{
	public static function load(string $filePath): array
	{
		$routes = [];
		$file = static::parseFile($filePath);
		$paths = $file['paths'] ?? [];
		foreach ($paths as $path => $pathData) {

			$route = new OASRoute($path);
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

	private static function addRouteData(OASRoute $route, string $httpMethod, array $pathDetails): void
	{
		$method = Method::from(strtoupper($httpMethod));
		$data = Data::create($method, $pathDetails);
		$route->setData($method, $data);
	}

	private static function addFromRef(OASRoute $route, string $ref, string $contractDirectory): void
	{
		[$relativeFilename, $refname] = explode('#/', $ref);
		$refFilepath = $contractDirectory . '/' . $relativeFilename;
		$file = static::parseFile($refFilepath);

		$paths = $file[$refname] ?? [];
		foreach ($paths as $httpMethod => $pathDetails)
			static::addRouteData($route, $httpMethod, $pathDetails);
	}

	private static function parseFile(string $filePath): array
	{
		$filePath = realpath($filePath);
		if (! $filePath)
			throw new Exception("File not found: $filePath", 500);

		$file = file_get_contents($filePath);
		$fileData = static::decodeFile($file, $filePath);
		if (! $fileData)
			throw new Exception("Invalid JSON: $filePath", 500);

		return $fileData;
	}

	private static function decodeFile(string $file, string $filePath): array
	{
		$extension = pathinfo($filePath, PATHINFO_EXTENSION);
		return match ($extension) {
			'json' => json_decode($file, associative: true) ?: [],
			'yaml', 'yml' => yaml_parse($file) ?: [],
			default => throw new Exception("Unsupported file type: $filePath", 500),
		};
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http\Route\Path;

use Projom\Http\Route\Path;
use Projom\Http\Route\Pattern;

class DynamicPath extends Path
{
	private const PARAMETER_IDENTIFIER_PATTERN = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	private readonly string $pattern;
	private readonly array $parameterIdentifiers;

	public function __construct(string $path, string $pattern, array $parameterIdentifiers)
	{
		$this->path = $path;
		$this->pattern = $pattern;
		$this->parameterIdentifiers = $parameterIdentifiers;
	}

	public static function create(string $path): DynamicPath
	{
		$pattern = Pattern::create($path);
		[$path, $parameterIdentifiers] = static::pathWithIdentifiers($path, static::PARAMETER_IDENTIFIER_PATTERN);
		return new DynamicPath($path, $pattern, $parameterIdentifiers);
	}

	private static function pathWithIdentifiers(string $path, string $paramterIdentifierPattern): array
	{
		$parameterIdentifiers = [];
		$pos = 1;
		$routePath = preg_replace_callback(
			$paramterIdentifierPattern,
			function ($matches) use (&$pos, &$parameterIdentifiers) {

				$type = $matches[1];
				$pattern = '{' . $type . '}';

				// If the identifier is not set, use a positional numeric.
				$identifier = $matches[2] ?? $pos;
				$parameterIdentifiers[] = $identifier;

				$pos++;
				return $pattern;
			},
			$path
		);

		return [$routePath, $parameterIdentifiers];
	}

	public function test(string $requestPath): array
	{
		if (preg_match($this->pattern, $requestPath, $matches) === 0)
			return [false, []];

		$parameters = $this->formatParameters($matches);
		return [true, $parameters];
	}

	private function formatParameters(array $parameters): array
	{
		// Remove the full string match.
		$parameters = array_slice($parameters, 1);

		if (!$parameters)
			return [];

		// Rekey the parameters with the identifiers.
		$parameters = array_combine($this->parameterIdentifiers, $parameters);

		return $parameters;
	}
}

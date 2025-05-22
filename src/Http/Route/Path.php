<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Pattern;

class Path
{
	private const PREPARE_ROUTE_PATH_IDENTIFIER = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	private string $pattern = '';
	private array $parameterIdentifiers = [];

	public function __construct(private string $path)
	{
		$this->path = $this->prepareParameterIdentifiers($path);
		$this->pattern = Pattern::create($this->path);
	}

	public static function create(string $path): Path
	{
		return new Path($path);
	}

	public function test(string $requestPath): array
	{
		if (preg_match($this->pattern, $requestPath, $matches) === 0)
			return [false, []];

		$parameters = $this->formatParameters($matches);

		return [true, $parameters];
	}

	public function formatParameters(array $parameters): array
	{
		// Remove the first element, the full string match.
		$parameters = array_slice($parameters, 1);

		if (!$parameters)
			return [];

		// Rekey the parameters with the identifiers.
		$parameters = array_combine($this->parameterIdentifiers, $parameters);

		return $parameters;
	}

	public function prepareParameterIdentifiers(string $path): string
	{
		$counter = 1;
		$routePath = preg_replace_callback(
			static::PREPARE_ROUTE_PATH_IDENTIFIER,
			function ($matches) use (&$counter) {

				$type = $matches[1];
				$pattern = '{' . $type . '}';

				// If the identifier is not set, use a positional numeric.
				$identifier = $matches[2] ?? $counter;
				$this->parameterIdentifiers[] = $identifier;

				$counter++;
				return $pattern;
			},
			$path
		);

		return $routePath;
	}
}

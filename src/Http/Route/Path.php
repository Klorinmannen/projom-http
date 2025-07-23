<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Pattern;

class Path
{
	private const PARAMETER_IDENTIFIER_PATTERN = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	private readonly string $path;
	private readonly bool $isStatic;
	private readonly string $pattern;
	private readonly array $parameterIdentifiers;

	public function __construct(string $path)
	{
		$this->path = $path;
		$this->isStatic = Util::isPathStatic($path);
		if (!$this->isStatic) {
			$this->pattern = Pattern::create($path);
			[$this->path, $this->parameterIdentifiers] = Util::pathWithIdentifiers($path, static::PARAMETER_IDENTIFIER_PATTERN);
		}
	}

	public static function create(string $path): Path
	{
		return new Path($path);
	}

	public function test(string $requestPath): array
	{
		if ($this->isStatic)
			return $this->path === $requestPath
				? [true, []]
				: [false, []];

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

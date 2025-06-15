<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Pattern;

class Path
{
	private const PARAMETER_IDENTIFIER_PATTERN = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	private readonly bool $isStatic;
	private readonly string $pattern;
	private array $parameterIdentifiers = [];

	public function __construct(private string $path)
	{
		$this->isStatic = $this->isStatic();

		if (! $this->isStatic) {
			$this->path = $this->withPathIdentifiers($path);
			$this->pattern = Pattern::create($this->path);
		}
	}

	public static function create(string $path): Path
	{
		return new Path($path);
	}

	private function isStatic(): bool
	{
		return str_contains($this->path, '{') && str_contains($this->path, '}');
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

	private function withPathIdentifiers(string $path): string
	{
		$pos = 1;
		$routePath = preg_replace_callback(
			static::PARAMETER_IDENTIFIER_PATTERN,
			function ($matches) use (&$pos) {

				$type = $matches[1];
				$pattern = '{' . $type . '}';

				// If the identifier is not set, use a positional numeric.
				$identifier = $matches[2] ?? $pos;
				$this->parameterIdentifiers[] = $identifier;

				$pos++;
				return $pattern;
			},
			$path
		);

		return $routePath;
	}
}

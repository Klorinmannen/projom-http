<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route\Path;

use Projom\Http\Router\ParameterType;
use Projom\Http\Router\Route\Path;
use Projom\Http\Router\Route\Path\Pattern;

class DynamicPath extends Path
{
	private const PARAMETER_IDENTIFIER_PATTERN = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	private readonly string $pattern;
	private readonly array $parameterIdentifiers;

	public function __construct(string $path, string $pattern, array $parameterIdentifiers)
	{
		parent::__construct($path);
		$this->pattern = $pattern;
		$this->parameterIdentifiers = $parameterIdentifiers;
	}

	public static function create(string $path): DynamicPath
	{
		[$pattern, $parameterIdentifiers] = static::patternhWithIdentifiers($path);
		$dynamicPath = new DynamicPath($path, $pattern, $parameterIdentifiers);
		return $dynamicPath;
	}

	private static function createSubstitutePattern(string $value): string
	{
		$substitute = static::createSubstitute($value);
		$substitutePattern = "/$substitute/";
		return $substitutePattern;
	}

	private static function patternhWithIdentifiers(string $path): array
	{
		[$path, $parameterIdentifiers] = static::substitutePathWithIdentifiers($path);
		$pattern = static::buildPattern($path);
		return [$pattern, $parameterIdentifiers];
	}

	private static function substitutePathWithIdentifiers(string $path): array
	{
		$parameterIdentifiers = [];
		$pos = 1;
		$path = preg_replace_callback(
			static::PARAMETER_IDENTIFIER_PATTERN,
			function ($matches) use (&$pos, &$parameterIdentifiers) {

				// Create substitute for the type.
				$type = (string)$matches[1];
				$substitute = static::createSubstitute($type);

				// If the identifier is not set, use a positional numeric.
				$identifier = $matches[2] ?? $pos;
				$parameterIdentifiers[] = $identifier;

				$pos++;
				return $substitute;
			},
			$path
		);
		return [$path, $parameterIdentifiers];
	}

	private static function buildPattern(string $path): string
	{
		$pattern = $path;
		foreach (ParameterType::cases() as $case)
			$pattern = preg_replace(
				static::createSubstitutePattern($case->value),
				$case->toPattern(),
				$pattern
			);

		$pattern = preg_replace('/\//', '\/', $pattern);
		$pattern = "/^$pattern$/";
		return $pattern;
	}

	private static function createSubstitute(string $value): string
	{
		return '{' . $value . '}';
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

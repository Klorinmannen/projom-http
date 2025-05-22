<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Pattern
{
	const FIND_NAMES = '/\{([^:{}]+|[^{}]+?):([^{}]+)\}/';
	const REMOVE_NAMES = '/:([^{}]+)(?=\})/';
	const PREPARE_ROUTE_PATH_NAMES = '/\{([^:{}]+(?:[^{}]*?))(?:\:([^{}]+))?\}/';

	const NUMERIC_ID = 'numeric_id';
	const INTEGER = 'integer';
	const STRING = 'string';
	const NAME = 'name';
	const BOOL = 'bool';

	private const DEFAULT_PARAMETER_PATTERNS = [
		'numeric_id' => '([1-9][0-9]+|[1-9]+)',
		'integer' => '([0-9]+)',
		'string' => '(.+)',
		'name' => '([a-zA-Z,_]+)',
		'bool' => '(true|false)',
	];

	public static function create(string $routePath): string
	{
		$pattern = preg_replace(static::REMOVE_NAMES, '', $routePath);
		foreach (static::DEFAULT_PARAMETER_PATTERNS as $name => $namePattern)
			$pattern = preg_replace(static::finalizeName($name), $namePattern, $pattern);

		$pattern = preg_replace('/\//', '\/', $pattern);

		return static::finalize($pattern);
	}

	private static function finalizeName(string $name): string
	{
		return '/' . '{' . $name . '}' . '/';
	}

	private static function finalize(string $pattern): string
	{
		if (! $pattern)
			return '';
		return "/^$pattern$/";
	}

	public static function test(string $pattern, string $subject): bool
	{
		if (! $pattern || ! $subject)
			return false;
		return preg_match($pattern, $subject) === 1;
	}

	public static function fromType(string $type): string
	{
		$type = strtolower($type);
		$pattern = match ($type) {
			static::NUMERIC_ID => static::DEFAULT_PARAMETER_PATTERNS['numeric_id'],
			static::INTEGER => static::DEFAULT_PARAMETER_PATTERNS['integer'],
			static::NAME => static::DEFAULT_PARAMETER_PATTERNS['name'],
			static::STRING => static::DEFAULT_PARAMETER_PATTERNS['string'],
			static::BOOL => static::DEFAULT_PARAMETER_PATTERNS['bool'],
			default => $type,
		};
		return static::finalize($pattern);
	}
}

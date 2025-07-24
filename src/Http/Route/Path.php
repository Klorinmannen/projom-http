<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Path\DynamicPath;
use Projom\Http\Route\Path\StaticPath;

/*
	Base class for defining route paths.
*/
abstract class Path
{
	public function __construct(protected readonly string $path) {}

	abstract public function test(string $requestPath): array;

	public static function create(string $path): Path
	{
		if (static::isDynamic($path))
			return DynamicPath::create($path);
		return StaticPath::create($path);
	}

	public static function isDynamic(string $path): bool
	{
		return str_contains($path, '{') && str_contains($path, '}');
	}
}

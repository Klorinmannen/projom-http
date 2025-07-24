<?php

declare(strict_types=1);

namespace Projom\Http\Route\Path;

use Projom\Http\Route\Path;

class StaticPath extends Path
{
	public static function create(string $path): StaticPath
	{
		return new StaticPath($path);
	}

	public function test(string $requestPath): array
	{
		if ($this->path === $requestPath)
			return [true, []];
		return [false, []];
	}
}

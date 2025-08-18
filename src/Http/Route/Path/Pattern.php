<?php

declare(strict_types=1);

namespace Projom\Http\Route\Path;

use Projom\Http\Route\ParameterType;

class Pattern
{
	public static function build(string $path): string
	{
		$pattern = $path;
		foreach (ParameterType::cases() as $case)
			$pattern = preg_replace(
				$case->toSubstitute(),
				$case->toPattern(),
				$pattern
			);

		$pattern = preg_replace('/\//', '\/', $pattern);
		$pattern = "/^$pattern$/";
		return $pattern;
	}
}

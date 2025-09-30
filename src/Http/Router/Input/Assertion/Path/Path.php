<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input\Assertion\Path;

use Projom\Http\Router\Input\Assertion\Util;

class Path
{
	public static function verify(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input path parameter set cannot be bigger than the expected set.
		if (count($inputParameters) != count($normalizedParameterDefinitions))
			return false;

		$result = Util::verify($inputParameters, $normalizedParameterDefinitions);
		if (!$result)
			return false;

		return true;
	}
}

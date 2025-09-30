<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input\Assertion\Parameter;

use Projom\Http\Router\Input\Assertion\Util;

class Mandatory
{
	public static function verify(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input parameter set count cannot be different than the expected set count.
		if (count($inputParameters) !== count($normalizedParameterDefinitions))
			return false;

		$namedDefinitions = Util::rekeyOnName($normalizedParameterDefinitions);

		// On the contrary to the required definition, the input parameters must be the exact same set as the mandatory definition.
		// Checking against the whole input set.
		$isSameSet = Util::isSameSet($inputParameters, $namedDefinitions);
		if (!$isSameSet)
			return false;

		$result = Util::verify($inputParameters, $namedDefinitions);
		if (!$result)
			return false;

		return true;
	}
}

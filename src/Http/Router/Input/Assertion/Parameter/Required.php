<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input\Assertion\Parameter;

use Projom\Http\Router\Input\Assertion\Util;

class Required
{
	public static function verify(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input parameter set cannot be less (or missing completely) than the definition set.
		if (count($inputParameters) < count($normalizedParameterDefinitions))
			return false;

		$namedDefinitions = Util::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = Util::selectSubset($inputParameters, $namedDefinitions);

		// The named definitions must be the same set as the found subset (meaning: all required parameters are present).
		// There might be more parameters in the input than the required definition set, so we check against the subset instead of the whole input.
		$isSameSet = Util::isSameSet($namedDefinitions, $namedDefinitionSubset);
		if (!$isSameSet)
			return false;

		$result = Util::verify($inputParameters, $namedDefinitionSubset);
		if (!$result)
			return false;

		return true;
	}
}

<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input\Assertion\Parameter;

use Projom\Http\Router\Input\Assertion\Util;

class Optional
{
	public static function verify(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;
		if (!$inputParameters)
			return true;

		$namedDefinitions = Util::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = Util::selectSubset($inputParameters, $namedDefinitions);

		// If theres no matching subset between the input and the named definition set, there's nothing to verify.
		if (!$namedDefinitionSubset)
			return true;

		$result = Util::verify($inputParameters, $namedDefinitionSubset);
		if (!$result)
			return false;

		return true;
	}
}

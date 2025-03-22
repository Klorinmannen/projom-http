<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Pattern;

class Parameter
{
    public static function verifyPath(array $inputParameters, array $expectedQueryParameters): bool
    {
        // Nothing to check.
        if (! $expectedQueryParameters)
            return true;

        // The input path parameter set cannot be bigger than the defined contract set.
        if (count($expectedQueryParameters) != count($inputParameters))
            return false;

        // Test input parameters.
        foreach ($expectedQueryParameters as $id => $parameterContract) {

            // Parameter is required but not present.
            if ($parameterContract['required'] && !$inputParameters[$id])
                return false;

            $result = static::verify((string) $inputParameters[$id], $parameterContract['type']);
            if (!$result)
                return false;
        }

        return true;
    }

	public static function verifyQuery(array $inputQueryParameters, array $expectedQueryParameters): bool
	{
		// Nothing to check.
		if (! $expectedQueryParameters)
			return true;

		// No input query parameters but we are expecting.
		if (! $inputQueryParameters && $expectedQueryParameters)
			return false;

		// The input query parameter set cannot be bigger than the expected set.
		if (count($inputQueryParameters) > count($expectedQueryParameters))
			return false;

		$namedExpectedQueryParameter = static::rekeyOnName($expectedQueryParameters);

		// Is the input query parameters a subset of the expected set. 
		// Atleast one of the expected query parameters must be present in the input query parameters.
		$isSubset = static::isSubset($inputQueryParameters, $namedExpectedQueryParameter);
		if (! $isSubset)
			return false;

		$namedExpectedQueryParameterSubset = static::selectSubset($inputQueryParameters, $namedExpectedQueryParameter);

		// Test the input query parameters.
		foreach ($namedExpectedQueryParameterSubset as $name => $parameterData) {

			// Parameter is required but not present.
			if ($parameterData['required'])
				if (! array_key_exists($name, $inputQueryParameters))
					return false;

			$result = static::verify((string) $inputQueryParameters[$name], $parameterData['type']);
			if (! $result)
				return false;
		}

		return true;
	}

	private static function rekeyOnName(array $expectedQueryParameters): array
	{
		return array_column($expectedQueryParameters, null, 'name');
	}

	public static function normalize(array $expectedParameters): array
	{
		$normalized = [];
		foreach ($expectedParameters as $name => $type)
			$normalized[] = [
				'name' => $name,
				'type' => $type,
				'required' => true
			];
		return $normalized;
	}

	private static function isSubset(array $inputParameters, array $namedExpectedQueryParameters): bool
	{
		$diff = array_diff_key($inputParameters, $namedExpectedQueryParameters);
		return count($diff) < count($namedExpectedQueryParameters);
	}

	private static function selectSubset(array $inputParameters, array $namedExpectedQueryParameters): array
	{
		return array_intersect_key($namedExpectedQueryParameters, $inputParameters);
	}

	private static function verify(string $inputParameter, string $type): bool
	{
		$parameterPattern = Pattern::fromType($type);
		return Pattern::test($parameterPattern, $inputParameter);
	}
}

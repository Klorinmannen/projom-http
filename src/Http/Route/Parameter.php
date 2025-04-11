<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Pattern;

class Parameter
{
	public static function verifyPath(array $inputParameters, array $expectedQueryParameters): bool
	{
		// Nothing to check against.
		if (! $expectedQueryParameters)
			return true;

		// The input path parameter set cannot be bigger than the expected set.
		if (count($inputParameters) != count($expectedQueryParameters))
			return false;

		$result = static::test($inputParameters, $expectedQueryParameters);
		if (! $result)
			return false;

		return true;
	}

	private static function test(array $inputParameters, array $expectedParameters): bool
	{
		foreach ($expectedParameters as $id => $parameterData) {

			// Parameter is required but not present.
			if ($parameterData['required'])
				if (! array_key_exists($id, $inputParameters))
					return false;

			$result = static::verify((string) $inputParameters[$id], $parameterData['type']);
			if (! $result)
				return false;
		}

		return true;
	}

	public static function verifyQuery(array $inputQueryParameters, array $expectedQueryParameters): bool
	{
		// Nothing to check against.
		if (! $expectedQueryParameters)
			return true;

		/* 
			Note: 
			This check can be removed or conditioned to make the input query parameters optional.
			Making the check less strict and allows for more flexibility.
		*/
		if (! $inputQueryParameters && $expectedQueryParameters)
			return false;

		// The input query parameter set cannot be bigger than the expected set.
		if (count($inputQueryParameters) > count($expectedQueryParameters))
			return false;

		$namedExpectedQueryParameter = static::rekeyOnName($expectedQueryParameters);

		/* 
			Note: 
			This check can be removed or conditioned, allowing for unknown parameters to be present in the set.
			Making the check less strict and allows for more flexibility.
			The selectSubset method will filter out the unknown parameters instead.
		*/
		$isSubset = static::isSubset($inputQueryParameters, $namedExpectedQueryParameter);
		if (! $isSubset)
			return false;

		$namedExpectedQueryParameterSubset = static::selectSubset($inputQueryParameters, $namedExpectedQueryParameter);

		$result = static::test($inputQueryParameters, $namedExpectedQueryParameterSubset);
		if (! $result)
			return false;

		return true;
	}

	private static function rekeyOnName(array $expectedQueryParameters): array
	{
		$namedExpectedQueryParameter = array_column($expectedQueryParameters, null, 'name');
		return $namedExpectedQueryParameter;
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

	private static function isSubset(array $inputQueryParameters, array $namedExpectedQueryParameters): bool
	{
		// The input query parameters must be a subset of the expected query parameters.
		// Any extra query parameters are not allowed.
		$diff = array_diff_key($inputQueryParameters, $namedExpectedQueryParameters);
		$isSubset = count($diff) === 0;
		return $isSubset;
	}

	private static function selectSubset(array $inputQueryParameters, array $namedExpectedQueryParameters): array
	{
		$subset = array_intersect_key($namedExpectedQueryParameters, $inputQueryParameters);
		return $subset;
	}

	private static function verify(string $inputParameter, string $type): bool
	{
		$parameterPattern = Pattern::fromType($type);
		return Pattern::test($parameterPattern, $inputParameter);
	}
}

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

	public static function verifyOptionalParameters(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (! $normalizedParameterDefinitions)
			return true;
		if (! $inputParameters)
			return true;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

		$result = static::test($inputParameters, $namedDefinitionSubset);
		if (! $result)
			return false;

		return true;
	}

	public static function verifyExpectedParameters(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (! $normalizedParameterDefinitions)
			return true;

		// The input parameter set cannot be empty if definitions are set.
		if (! $inputParameters && $normalizedParameterDefinitions)
			return false;

		// The input parameter set cannot be bigger than the expected set.
		if (count($inputParameters) > count($normalizedParameterDefinitions))
			return false;


		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);

		$isSubset = static::isSubset($inputParameters, $namedDefinitions);
		if (! $isSubset)
			return false;

		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

		$result = static::test($inputParameters, $namedDefinitionSubset);
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

	private static function isSubset(array $inputParameters, array $namedExpectedParameters): bool
	{
		// The input parameters must be a subset of the expected parameters.
		// Any extra parameters are not allowed.
		$diff = array_diff_key($inputParameters, $namedExpectedParameters);
		$isSubset = count($diff) === 0;
		return $isSubset;
	}

	private static function selectSubset(array $inputParameters, array $namedExpectedParameters): array
	{
		$subset = array_intersect_key($namedExpectedParameters, $inputParameters);
		return $subset;
	}

	private static function verify(string $inputParameter, string $type): bool
	{
		$parameterPattern = Pattern::fromType($type);
		return Pattern::test($parameterPattern, $inputParameter);
	}
}

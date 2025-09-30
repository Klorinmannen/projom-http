<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input\Assertion;

use Projom\Http\Router\ParameterType;

class Util
{
	public static function verify(array $inputParameters, array $normalizedParameterData): bool
	{
		foreach ($normalizedParameterData as $id => $parameterData) {

			// Parameter is required but not present.
			if ($parameterData['required'])
				if (!array_key_exists($id, $inputParameters))
					return false;

			$subject = (string)($inputParameters[$id] ?? '');
			$result = static::test($parameterData['type'], $subject);
			if (!$result)
				return false;
		}

		return true;
	}

	private static function test(ParameterType $pathParameter, string $subject): bool
	{
		$result = preg_match($pathParameter->toPattern(), $subject) === 1;
		return $result;
	}

	public static function rekeyOnName(array $parameterDefinitions): array
	{
		$namedParameterDefinitions = array_column($parameterDefinitions, null, 'name');
		return $namedParameterDefinitions;
	}

	public static function normalize(array $parameterDefinitions): array
	{
		$normalized = [];
		foreach ($parameterDefinitions as $name => $type)

			$normalized[] = [
				'name' => $name,
				'type' => static::normalizeParameterType($type),
				'required' => true
			];
		return $normalized;
	}

	/**
	 * This method tries to be lenient; accepting both ParameterType and strings to normalize the parameter type.
	 */
	public static function normalizeParameterType(ParameterType|string $type): ParameterType
	{
		if ($type instanceof ParameterType)
			return $type;

		// Throws ValueError if the type is not a valid ParameterType.
		$type = ParameterType::from($type);

		return $type;
	}

	public static function isSameSet(array $inputParameters, array $namedParameterDefinitions): bool
	{
		// The input parameters must be equal of the named parameter definitions.
		// Any missing or extra parameters are not allowed.
		$diff = array_diff_key($inputParameters, $namedParameterDefinitions);
		$isSameSet = count($diff) === 0;
		return $isSameSet;
	}

	public static function selectSubset(array $inputParameters, array $namedParameterDefinitions): array
	{
		$subset = array_intersect_key($namedParameterDefinitions, $inputParameters);
		return $subset;
	}
}

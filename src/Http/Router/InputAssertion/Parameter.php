<?php

declare(strict_types=1);

namespace Projom\Http\Router\InputAssertion;

use Projom\Http\Router\ParameterType;

class Parameter
{
	public static function verifyPath(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input path parameter set cannot be bigger than the expected set.
		if (count($inputParameters) != count($normalizedParameterDefinitions))
			return false;

		$result = static::verify($inputParameters, $normalizedParameterDefinitions);
		if (!$result)
			return false;

		return true;
	}

	private static function verify(array $inputParameters, array $normalizedParameterData): bool
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

	public static function verifyOptional(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;
		if (!$inputParameters)
			return true;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

		// If theres no matching subset between the input and the named definition set, there's nothing to verify.
		if (!$namedDefinitionSubset)
			return true;

		$result = static::verify($inputParameters, $namedDefinitionSubset);
		if (!$result)
			return false;

		return true;
	}

	public static function verifyRequired(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input parameter set cannot be less (or missing completely) than the definition set.
		if (count($inputParameters) < count($normalizedParameterDefinitions))
			return false;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

		// The named definitions must be the same set as the found subset (meaning: all required parameters are present).
		// There might be more parameters in the input than the required definition set, so we check against the subset instead of the whole input.
		$isSameSet = static::isSameSet($namedDefinitions, $namedDefinitionSubset);
		if (!$isSameSet)
			return false;

		$result = static::verify($inputParameters, $namedDefinitionSubset);
		if (!$result)
			return false;

		return true;
	}

	public static function verifyMandatory(array $inputParameters, array $normalizedParameterDefinitions): bool
	{
		// Nothing to check against.
		if (!$normalizedParameterDefinitions)
			return true;

		// The input parameter set count cannot be different than the expected set count.
		if (count($inputParameters) !== count($normalizedParameterDefinitions))
			return false;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);

		// On the contrary to the required definition, the input parameters must be the exact same set as the mandatory definition.
		// Checking against the whole input set.
		$isSameSet = static::isSameSet($inputParameters, $namedDefinitions);
		if (!$isSameSet)
			return false;

		$result = static::verify($inputParameters, $namedDefinitions);
		if (!$result)
			return false;

		return true;
	}

	private static function rekeyOnName(array $parameterDefinitions): array
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
	protected static function normalizeParameterType(ParameterType|string $type): ParameterType
	{
		if ($type instanceof ParameterType)
			return $type;

		// Throws ValueError if the type is not a valid ParameterType.
		$type = ParameterType::from($type);

		return $type;
	}

	private static function isSameSet(array $inputParameters, array $namedParameterDefinitions): bool
	{
		// The input parameters must be equal of the named parameter definitions.
		// Any missing or extra parameters are not allowed.
		$diff = array_diff_key($inputParameters, $namedParameterDefinitions);
		$isSameSet = count($diff) === 0;
		return $isSameSet;
	}

	private static function selectSubset(array $inputParameters, array $namedParameterDefinitions): array
	{
		$subset = array_intersect_key($namedParameterDefinitions, $inputParameters);
		return $subset;
	}
}

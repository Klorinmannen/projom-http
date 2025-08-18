<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\ParameterType;

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

		// The input parameter set cannot be empty if definitions are set.
		if (!$inputParameters && $normalizedParameterDefinitions)
			return false;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);
		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

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

		// The input parameter set cannot be empty if definitions are set.
		if (!$inputParameters && $normalizedParameterDefinitions)
			return false;

		// The input parameter set cannot be bigger than the expected set.
		if (count($inputParameters) > count($normalizedParameterDefinitions))
			return false;

		$namedDefinitions = static::rekeyOnName($normalizedParameterDefinitions);

		$isSubset = static::isSubset($inputParameters, $namedDefinitions);
		if (!$isSubset)
			return false;

		$namedDefinitionSubset = static::selectSubset($inputParameters, $namedDefinitions);

		$result = static::verify($inputParameters, $namedDefinitionSubset);
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

	private static function isSubset(array $inputParameters, array $namedParameterDefinitions): bool
	{
		// The input parameters must be a subset of the expected parameters.
		// Any extra parameters are not allowed.
		$diff = array_diff_key($inputParameters, $namedParameterDefinitions);
		$isSubset = count($diff) === 0;
		return $isSubset;
	}

	private static function selectSubset(array $inputParameters, array $namedParameterDefinitions): array
	{
		$subset = array_intersect_key($namedParameterDefinitions, $inputParameters);
		return $subset;
	}
}

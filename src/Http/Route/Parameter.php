<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Parameter
{
	public static function verifyQuery(array $inputQueryParameters, array $queryParameterDefinition): bool
	{
		// Nothing to check.
		if (! $queryParameterDefinition || ! $inputQueryParameters)
			return true;

		// The input query parameter set cannot be bigger than the defined set.
		if (count($inputQueryParameters) > count($queryParameterDefinition))
			return false;

		$queryParameterDefinition = static::normalize($queryParameterDefinition);

		// Rekey on name.
		$namedQueryParameterDefinitions = array_column($queryParameterDefinition, null, 'name');

		// Is the input query parameters a subset of the defined set.
		$isSubset = static::isSubset($inputQueryParameters, $namedQueryParameterDefinitions);
		if (! $isSubset)
			return false;

		// Select the input subset.
		$namedQueryParameterDefinitionSubset = array_intersect_key($namedQueryParameterDefinitions, $inputQueryParameters);

		// Test the input query parameters.
		foreach ($namedQueryParameterDefinitionSubset as $name => $parameterData) {

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

	private static function normalize(array $parameterDefinitions): array
	{
		$normalized = [];
		foreach ($parameterDefinitions as $name => $type)
			$normalized[] = [
				'name' => $name,
				'type' => $type,
				'required' => true
			];
		return $normalized;
	}

	private static function isSubset(array $inputParameters, array $namedQueryParameterDefinitions): bool
	{
		// The input query parameters must be a subset of the defined set.
		$diff = array_diff_key($inputParameters, $namedQueryParameterDefinitions);
		return count($diff) === 0;
	}

	private static function verify(string $inputParameter, string $type): bool
	{
        if (!$parameterPattern = Pattern::fromType($type))
            return false;
        return Pattern::test($parameterPattern, $inputParameter);
	}
}

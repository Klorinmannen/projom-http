<?php

declare(strict_types=1);

namespace Projom\Http\OAS\Route;

use Projom\Http\Router\InputAssertion\Parameter as P;

class Parameter extends P
{
    public static function normalize(array $parameterDefinitions): array
    {
        $normalized = [];
        foreach ($parameterDefinitions as $parameterData) {

            $in = $parameterData['in']; // 'path' or 'query'.
            $name = $parameterData['name'] ?? '';
            $type = $parameterData['schema']['type'] ?? '';

            // Default to required: true for stricter safety precaution.
            $required = (bool) ($parameterData['required'] ?? true);

            $normalized[$in][] = [
                'name' => $name,
                'type' => static::normalizeParameterType($type),
                'required' => $required
            ];
        }

        return $normalized;
    }
}

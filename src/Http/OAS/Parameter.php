<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Route\Parameter as RouteParameter;

class Parameter extends RouteParameter
{
    public static function normalize(array $expectedParameters): array
    {
        $normalized = [];
        foreach ($expectedParameters as $parameterData) {

            $in = $parameterData['in'];
            $name = $parameterData['name'] ?? '';
            $type = $parameterData['schema']['type'] ?? '';

            // Default to required - true, stricter safety precaution.
            $required = (bool) ($parameterData['required'] ?? true);

            $normalized[$in][] = [
                'name' => $name,
                'type' => $type,
                'required' => $required
            ];
        }

        return $normalized;
    }
}

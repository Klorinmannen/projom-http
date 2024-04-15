<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Http\Api\Pattern;
use Projom\Util\Bools;
use Projom\Util\Math;

class ParameterContract
{
    private array $parameterContracts = [];

    public function __construct(array $parameterContracts = [])
    {
        $this->parameterContracts = $this->parseList($parameterContracts);
    }

    public static function create(array $parameterContracts = []): ParameterContract
    {
        return new ParameterContract($parameterContracts);
    }

    public function parseList(array $paraameterContracts): array
    {
        $parsedParaameterContracts = [];
        foreach ($paraameterContracts as $parameterContract) {
            $in = $parameterContract['in'];
            $parsedParaameterContracts[$in][] = $this->parse($parameterContract);
        }
        return $parsedParaameterContracts;
    }

    public function parse(array $parameterContract): array
    {
        $name = $parameterContract['name'] ?? '';
        $type = $parameterContract['schema']['type'] ?? '';
        $required = (bool) ($parameterContract['required'] ?? true);

        return [
            'name' => $name,
            'type' => $type,
            'required' => $required
        ];
    }

    public function verifyPath(array $inputParameters): bool
    {
        // Nothing to check.
        if (!$pathContracts = $this->parameterContracts['path'] ?? [])
            return true;

        // The input path parameter set cannot be bigger than the defined contract set.
        if (count($pathContracts) != count($inputParameters))
            return false;

        // Test input parameters.
        foreach ($pathContracts as $id => $parameterContract) {

            // Parameter is required but not present.
            if ($parameterContract['required'] && !$inputParameters[$id])
                return false;

            $result = $this->verify(
                (string)$inputParameters[$id],
                $parameterContract['type']
            );
            if (!$result)
                return false;
        }

        return true;
    }

    public function verify(string $inputParameter, string $parameterContractType): bool
    {
        if (!$parameterPattern = Pattern::fromType($parameterContractType))
            return false;
        return Pattern::test($parameterPattern, $inputParameter);
    }

    public function verifyQuery(array $inputParameters): bool
    {
        // Nothing to check.
        if (!$queryContracts = $this->parameterContracts['query'] ?? [])
            return true;

        // The input query parameter set cannot be bigger than the defined contract set.
        if (count($inputParameters) > count($queryContracts))
            return false;

        // Rekey on the name.
        $namedQueryContracts = array_column(
            $queryContracts,
            null,
            'name'
        );

        // Is the input query parameters a subset of the defined set.
        $isSubset = Math::isSubset(
            $inputParameters,
            $namedQueryContracts
        );
        if (!$isSubset)
            return false;

        // Select the input subset.
        $namedParameterContractSubset = array_intersect_key(
            $namedQueryContracts,
            $inputParameters
        );

        // Test the input query parameters.
        foreach ($namedParameterContractSubset as $name => $queryData) {

            if ($queryData['required'] && !$inputParameters[$name])
                return false;

            $result = $this->verify(
                (string)$inputParameters[$name],
                $queryData['type']
            );
            if (!$result)
                return false;
        }

        return true;
    }
}

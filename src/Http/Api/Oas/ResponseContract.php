<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

class ResponseContract
{
    private $responseContracts = [];

    public function  __construct(array $responseContracts)
    {
        $this->responseContracts = $this->parseList($responseContracts);
    }

    public static function create(array $responseContracts): ResponseContract
    {
        return new ResponseContract($responseContracts);
    }

    public function parseList(array $responseContracts): array
    {
        $contracts = [];
        foreach ($responseContracts as $statusCode => $responseContract)
            $contracts[$statusCode] = key($responseContract['content'] ?? []);
        return $contracts;
    }

    public function verify(
        int $statusCode,
        string $contentType
    ): bool {

        if (!$responseContractContentType = $this->responseContracts[$statusCode] ?? '')
            return false;

        if ($responseContractContentType !== $contentType)
            return false;

        return true;
    }
}

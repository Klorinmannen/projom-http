<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Header;
use Projom\Http\Input;

class Request
{
    protected null|Input $input = null;
    protected string $urlPath = '';
    protected array $parsedUrl = [];
    protected array $urlPathPartList = [];
    protected array $queryParameters = [];
    protected array $pathParameters = [];

    public function __construct(Input $input)
    {
        $this->input = $input;
        $this->parseUrl($input->url());
    }

    public static function create(null|Input $input = null): Request
    {
        if ($input !== null)
            return new Request($input);

        $input = Input::create($_REQUEST ?? [], $_SERVER ?? [], file_get_contents('php://input') ?: '');

        return new Request($input);
    }

    public function parseUrl(string $url): void
    {
        $this->parsedUrl = parse_url($url);

        $queryList = $this->parsedUrl['query'] ?? '';
        parse_str($queryList, $this->queryParameters);

        $this->urlPath = $this->parsedUrl['path'] ?? '';

        $urlPath = trim($this->urlPath, '/');
        $this->urlPathPartList = $urlPath ? explode('/', $urlPath) : [];
    }

    public function matchPattern(string $pattern): bool
    {
        if (!$pattern)
            return false;

        $result = preg_match($pattern, $this->urlPath, $this->pathParameters) === 1;
        if ($result)
            return true;

        return false;
    }

    public function empty(): bool
    {
        if (!$this->urlPath)
            return true;
        return false;
    }

    public function header(string $header): null|string
    {
        $headers = $this->input->headers();
        return $headers[$header] ?? null;
    }

    public function authToken(): string
    {
        if (!$authHeader = $this->header('HTTP_AUTHORIZATION'))
            return '';

        if (!$token = Header::parseBearerAuthToken($authHeader))
            return '';

        return $token;
    }

    public function payload(): string
    {
        return $this->input->payload();
    }

    public function url(): string
    {
        return $this->input->url();
    }

    public function httpMethod(): string
    {
        return $this->input->method();
    }

    public function method(): Method
    {
        return Method::tryFrom($this->httpMethod());
    }

    public function parsedUrl(): array
    {
        return $this->parsedUrl;
    }

    public function urlPath(): string
    {
        return $this->urlPath;
    }

    public function queryParameters(): array
    {
        return $this->queryParameters;
    }

    public function pathParameterList(): array
    {
        return $this->pathParameters;
    }

    public function urlPathPartList(): array
    {
        return $this->urlPathPartList;
    }
}

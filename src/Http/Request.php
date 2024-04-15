<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Header;
use Projom\Http\Input;

class Request
{
    protected $input = null;

    protected string $urlPath = '';
    protected array $parsedUrl = [];
    protected array $urlPathPartList = [];
    protected array $queryParameterList = [];
    protected array $pathParameterList = [];

    public function __construct(Input $input)
    {
        $this->input = $input;
        $this->parseUrl($input->url());
    }

    public static function create(): Request
    {
        $input = new Input(
            $_REQUEST ?? [],
            $_SERVER ?? []
        );
        return new Request($input);
    }

    public function parseUrl(string $url): void
    {
        $this->parsedUrl = parse_url($url);

        $queryList = $this->parsedUrl['query'] ?? '';
        parse_str($queryList, $this->queryParameterList);

        $this->urlPath = $this->parsedUrl['path'] ?? '';

        $urlPath = trim($this->urlPath, '/');
        $this->urlPathPartList = $urlPath ? explode('/', $urlPath) : [];
    }

    public function matchPattern(string $pattern): bool
    {
        if (!$pattern)
            return false;

        $result = preg_match($pattern, $this->urlPath, $this->pathParameterList) === 1;
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

    public function header(string $header): string
    {
        $headers = $this->input->headers();
        return $headers[$header] ?? '';
    }

    public function authToken(): string
    {
        if (!$authHeader = $this->header('HTTP_AUTHORIZATION'))
            return '';

        if (!$token = Header::parseAuthHeader($authHeader))
            return '';

        return $token;
    }

    public function payload(string $source = 'php://input'): string
    {
        return $this->input->data($source);
    }

    public function url(): string
    {
        return $this->input->url();
    }

    public function httpMethod(): string
    {
        return $this->input->method();
    }

    public function parsedUrl(): array
    {
        return $this->parsedUrl;
    }

    public function urlPath(): string
    {
        return $this->urlPath;
    }

    public function queryParameterList(): array
    {
        return $this->queryParameterList;
    }

    public function pathParameterList(): array
    {
        return $this->pathParameterList;
    }

    public function urlPathPartList(): array
    {
        return $this->urlPathPartList;
    }
}

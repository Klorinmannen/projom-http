<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request\Input;

class Request
{
    protected string $path = '';
    protected array $parsedUrl = [];
    protected array $headers = [];
    protected array $queryParameters = [];
    protected array $pathParameters = [];

    public function __construct(protected readonly null|Input $input)
    {
        $this->parseUrl();
        $this->parseHeaders();
    }

    public static function create(null|Input $input = null): Request
    {
        if ($input !== null)
            return new Request($input);

        $input = Input::create();
        $request = new Request($input);

        return $request;
    }

    private function parseUrl(): void
    {
        $this->parsedUrl = parse_url($this->input->server['REQUEST_URI'] ?? '');

        $queryParams = $this->parsedUrl['query'] ?? '';
        parse_str($queryParams, $this->queryParameters);

        $this->path = $this->parsedUrl['path'] ?? '';
    }

    private function parseHeaders(): void
    {
        if ($this->input === null)
            return;

        $pattern = '/^HTTP_.+$/';
        $serverKeys = array_keys($this->input->server);
        $foundHttpKeys = preg_grep($pattern, $serverKeys);
        $this->headers = array_intersect_key($this->input->server, array_flip($foundHttpKeys));
    }

    public function empty(): bool
    {
        return empty($this->path);
    }

    public function method(): null|Method
    {
        return Method::tryFrom($this->input->server['REQUEST_METHOD'] ?? '');
    }

    public function path(): string
    {
        return $this->path;
    }

    public function pathParameters(null|int|string $name = null): null|array|string
    {
        if ($name !== null)
            return $this->pathParameters[(string)$name] ?? null;
        return $this->pathParameters;
    }

    public function setPathParameters(array $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    public function queryParameters(string $name = ''): null|array|string
    {
        if ($name !== '')
            return $this->queryParameters[$name] ?? null;
        return $this->queryParameters;
    }

    public function vars(null|string $name = null, mixed $default = null): mixed
    {
        if ($name !== null)
            return $this->input->request[$name] ?? $default;
        return $this->input->request;
    }

    public function headers(null|string $header = null): null|array|string
    {
        if ($header !== null) {
            $header = $this->normalizeHeader($header);
            return $this->headers[$header] ?? null;
        }

        return $this->headers;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtoupper($header);
        $header = str_replace('-', '_', $header);

        if (! str_starts_with($header, 'HTTP_'))
            $header = 'HTTP_' . $header;

        return $header;
    }

    public function files(null|string $name = null): null|array
    {
        if ($name !== null)
            return $this->input->files[$name] ?? null;
        return $this->input->files;
    }

    public function cookies(null|string $name = null): null|array
    {
        if ($name !== null)
            return $this->input->cookies[$name] ?? null;
        return $this->input->cookies;
    }

    public function payload(): string
    {
        return $this->input->payload;
    }

    public function find(string $name): mixed
    {
        if (array_key_exists($name, $this->pathParameters))
            return $this->pathParameters[$name];

        if (array_key_exists($name, $this->queryParameters))
            return $this->queryParameters[$name];

        if (array_key_exists($name, $this->input->request))
            return $this->input->request[$name];

        if (array_key_exists($name, $this->input->files))
            return $this->input->files[$name];

        if (array_key_exists($name, $this->input->cookies))
            return $this->input->cookies[$name];

        if (array_key_exists($name, $this->headers))
            return $this->headers[$name];

        return null;
    }
}

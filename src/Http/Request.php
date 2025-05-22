<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request\Input;

class Request
{
    protected null|Input $input = null;
    protected string $path = '';
    protected array $parsedUrl = [];
    protected array $headers = [];
    protected array $queryParameters = [];
    protected array $pathParameters = [];

    public function __construct(Input $input)
    {
        $this->input = $input;
        $this->parseUrl();
        $this->parseHeaders();
    }

    public static function create(null|Input $input = null): Request
    {
        if ($input !== null)
            return new Request($input);

        $input = Input::create();

        return new Request($input);
    }

    private function parseUrl(): void
    {
        $this->parsedUrl = parse_url($this->input->server['REQUEST_URI'] ?? '');

        $queryList = $this->parsedUrl['query'] ?? '';
        parse_str($queryList, $this->queryParameters);

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

    public function headers(null|string $header = null): null|array|string
    {
        if ($header === null)
            return $this->headers;

        return $this->headers[$header] ?? null;
    }

    public function vars(null|string $name = null, mixed $default = null): mixed
    {
        if ($name === null)
            return $this->input->request;

        return $this->input->request[$name] ?? $default;
    }

    public function files(null|string $name = null): null|array
    {
        if ($name === null)
            return $this->input->files;

        return $this->input->files[$name] ?? null;
    }

    public function cookies(null|string $name = null): null|array
    {
        if ($name === null)
            return $this->input->cookies;

        return $this->input->cookies[$name] ?? null;
    }

    public function payload(): string
    {
        return $this->input->payload;
    }

    public function method(): null|Method
    {
        return Method::tryFrom($this->input->server['REQUEST_METHOD'] ?? '');
    }

    public function path(): string
    {
        return $this->path;
    }

    public function queryParameters(string $name = ''): null|array|string
    {
        if ($name !== '')
            return $this->queryParameters[$name] ?? null;
        return $this->queryParameters;
    }

    /**
     * @param null|int $name
     * 
     * * Example: $request->pathParameters() will return all path parameters.
     * * Example: $request->pathParameters('id') will return the path parameter identified by 'id'.
     */
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
}

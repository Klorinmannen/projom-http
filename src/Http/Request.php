<?php

declare(strict_types=1);

namespace Projom\Http;

use SensitiveParameter;

use Projom\Http\Method;
use Projom\Http\ResponseBase;
use Projom\Http\Request\Header;
use Projom\Http\Request\Input;
use Projom\Http\Request\Timer;

class Request
{
    protected readonly Input $input;
    protected readonly Timer $timer;
    protected readonly Header $header;
    protected null|ResponseBase $response = null;
    protected readonly string $path;
    protected readonly array $queryParameters;
    protected readonly array $pathParameters;

    public function __construct(#[SensitiveParameter] Input $input)
    {
        $this->input = $input;
        $this->timer = Timer::create();
        $this->parseUrl();
        $this->header = Header::create($this->input->server);
    }

    public static function create(#[SensitiveParameter] null|Input $input = null): Request
    {
        if ($input !== null)
            return new Request($input);

        $input = Input::create();
        $request = new Request($input);

        return $request;
    }

    private function parseUrl(): void
    {
        $parsedUrl = parse_url($this->input->server['REQUEST_URI'] ?? '');
        $this->path = $parsedUrl['path'] ?? '';

        $queryParameters = [];
        parse_str($parsedUrl['query'] ?? '', $queryParameters);
        $this->queryParameters = $queryParameters;
    }

    public function empty(): bool
    {
        return empty($this->path);
    }

    public function method(bool $asString = false): null|string|Method
    {
        if ($asString)
            return $this->input->server['REQUEST_METHOD'] ?? null;
        return Method::tryFrom($this->input->server['REQUEST_METHOD'] ?? '');
    }

    public function path(): string
    {
        return $this->path;
    }

    public function ip(): string
    {
        return $this->input->server['REMOTE_ADDR'] ?? '';
    }

    public function setPathParameters(array $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    public function pathParameters(null|int|string $name = null): null|array|string
    {
        if ($name !== null)
            return $this->pathParameters[(string)$name] ?? null;
        return $this->pathParameters;
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
        return $this->header->get($header);
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

        if ($this->header->exists($name))
            return $this->header->get($name);

        return null;
    }

    public function timer(): Timer
    {
        return $this->timer;
    }

    public function setResponse(#[SensitiveParameter] ResponseBase $response): void
    {
        $this->response = $response;
    }

    public function response(): null|ResponseBase
    {
        return $this->response;
    }
}

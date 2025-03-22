<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Input;

class Request
{
    protected null|Input $input = null;
    protected string $path = '';
    protected array $parsedUrl = [];
    protected array $queryParameters = [];

    public function __construct(Input $input)
    {
        $this->input = $input;
        $this->parseUrl();
    }

    public static function create(null|Input $input = null): Request
    {
        if ($input !== null)
            return new Request($input);

        $input = Input::create($_REQUEST ?? [], $_SERVER ?? [], file_get_contents('php://input') ?: '');

        return new Request($input);
    }

    private function parseUrl(): void
    {
        $this->parsedUrl = parse_url($this->input->url());

        $queryList = $this->parsedUrl['query'] ?? '';
        parse_str($queryList, $this->queryParameters);

        $this->path = $this->parsedUrl['path'] ?? '';
    }

    public function empty(): bool
    {
        return empty($this->path);
    }

    public function header(string $header): null|string
    {
        $headers = $this->input->headers();
        return $headers[$header] ?? null;
    }

    public function payload(): string
    {
        return $this->input->payload();
    }

    public function method(): Method
    {
        return Method::from($this->input->method());
    }

    public function path(): string
    {
        return $this->path;
    }

    public function queryParameters(): array
    {
        return $this->queryParameters;
    }
}

<?php

declare(strict_types=1);

namespace Projom\Http\Auth;

use Projom\Http\Auth\Jwt\Signature;
use Projom\Http\Auth\Jwt\Util;

class Jwt
{
    private string $token = '';
	private array $payload = [];
	private array $header = [];
    private string $base64UrlHeader = '';
    private string $base64Payload = '';
	private string $signature = '';

	public function __construct(string $token)
	{
		$this->token = $token;
        $this->decode($token);
	}

    private function decode(string $token): void
    {
        if (!$parts = $this->parts($token))
            return;

        [$base64UrlHeader, $base64Payload, $signature] = $parts;

        $this->$base64UrlHeader = $base64UrlHeader;
        $this->$base64Payload = $base64Payload;
        $this->signature = $signature;
        $this->header = Util::decodeBase64url($base64UrlHeader);
        $this->payload = Util::decodeBase64url($base64Payload);
    }

    private function parts(string $token): array
    {
        $parts = explode('.', $token);
        if (!$parts)
            return [];

        if (count($parts) != 3)
            return [];

        [$base64UrlHeader, $base64Payload, $signature] = $parts;
        if (!$base64UrlHeader || !$base64Payload || !$signature)
            return [];

        return $parts;
    }

    public function verify(array $claims): bool
    {
        if (!$claims)
            return false;

        if (!$this->token)
            return false;

        $testSignature = Signature::create(
            $this->base64UrlHeader,
            $this->base64Payload,
            $claims['alg'] ?? '',
            $claims['secret'] ?? ''
        );

        $result = Signature::compare($testSignature, $this->signature);
        if ($result === false)
            return false;

        $now = strtotime('now');
        $expiresAt = (int)$this->payload['exp'] ?? 0;
        if (static::isExpired($expiresAt, $now))
            return false;

        return true;
    }

    public function isExpired(int $expiresAt, int $now): bool
    {
        return $now >= $expiresAt;
    }

    public function empty(): bool
	{
		return $this->token ? false : true;
	}

	public function token(): string
	{
		return $this->token;
	}

	public function payload(string $key): mixed
	{
		return $this->payload[$key] ?? null;
	}

    public function header(string $key): mixed
    {
        return $this->header[$key] ?? null;
    }

    public function signature(): string
    {
        return $this->signature;
    }
}

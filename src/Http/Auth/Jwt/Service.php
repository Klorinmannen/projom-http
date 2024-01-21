<?php

declare(strict_types=1);

namespace Projom\Http\Auth\Jwt;

use Projom\Http\Auth\Jwt;
use Projom\Http\Auth\Jwt\Header;
use Projom\Http\Auth\Jwt\Payload;
use Projom\Http\Auth\Jwt\Signature;

class Service
{
	private array $claims = [];

	public function __construct(array $claims)
	{
		$this->claims = $claims;
	}

	public function create(array $userClaims): ?Jwt
	{
		if (!$this->claims)
			return null;

		if (!$userID = $userClaims['userID'] ?? 0)
			return null;

		$base64urlHeader = Header::create(
			$this->claims['alg'],
			$this->claims['type']
		);

		$base64urlPayload = Payload::create(
			$this->claims['issuer'],
			$userID,
			$this->claims['expiration'],
		);

		$signature = Signature::create(
			$base64urlHeader,
			$base64urlPayload,
			$this->claims['alg'],
			$this->claims['secret']
		);

		$token = $base64urlHeader . '.' . $base64urlPayload . '.' . $signature;

		return new Jwt($token);
	}

	public function verify(Jwt $token): bool
    {
		if (!$this->claims)
			return false;

		if (!$token)
            return false;

		if ($token->empty())
			return false;

		return $token->verify($this->claims);
    }
}

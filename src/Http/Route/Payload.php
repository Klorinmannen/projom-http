<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Payload
{
	public static function verify(string $payload, bool $expectedPayload): bool
	{
		if ($expectedPayload)
			if (empty($payload))
				return false;

		return true;
	}
}

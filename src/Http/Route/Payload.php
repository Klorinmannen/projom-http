<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Payload
{
	public static function verify(string $payload, bool $isRequired): bool
	{
		if ($isRequired)
			if (empty($payload))
				return false;
		return true;
	}
}

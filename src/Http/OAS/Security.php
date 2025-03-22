<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

class Security
{
	public static function normalize(array $security): bool 
	{
		return ($security ?? false) ? true : false;
	}
}
<?php

declare(strict_types=1);

namespace Projom\Http\OAS\Route;

class Security
{
	public static function normalize(array $security): bool 
	{
		return $security ? true : false;
	}
}
<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Input;
use Projom\Http\Request;

class RequestFactory
{
	public static function create(): Request
	{
		$input = new Input(
			$_REQUEST ?? [],
			$_SERVER ?? []
		);
		return new Request($input);
	}
}

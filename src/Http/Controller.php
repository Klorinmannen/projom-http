<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request;

abstract class Controller
{
	public function __construct(protected Request $request) {}
}

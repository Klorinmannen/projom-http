<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Projom\Http\Request;

interface MiddlewareInterface
{
	public function process(Request $request): void;
}

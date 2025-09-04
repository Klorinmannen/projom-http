<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Projom\Http\Request;
use Projom\Http\Router\Route\RouteBase;

interface InputAssertionInterface
{
	/**
	 * Verify the request against the route's requirements.
	 */
	public function verify(Request $request, RouteBase $route): void;
}

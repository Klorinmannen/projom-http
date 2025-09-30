<?php

declare(strict_types=1);

namespace Projom\Http\Router\Input;

use Projom\Http\Request;
use Projom\Http\Router\Route\Input\Definition;

interface AssertionInterface
{
	/**
	 * Verify the request against the route's requirements.
	 */
	public function verify(Request $request, Definition $inputDefinition): void;
}

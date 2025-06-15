<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request;
use Projom\Http\Response;

interface MiddlewareInterface
{
	public function processBeforeRouting(Request $request): void;
	public function processAfterRouting(Request $request, Response $response): void;
}

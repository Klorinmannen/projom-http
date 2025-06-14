<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request;
use Projom\Http\Response;

interface MiddlewareInterface
{
	public function process(Request|Response $message): void;
}

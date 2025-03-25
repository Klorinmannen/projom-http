<?php

declare(strict_types=1);

namespace Projom\Http;

interface MiddlewareInterface
{
	public function process(Request $request): void;
}

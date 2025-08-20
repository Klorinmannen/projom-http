<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Middleware;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Middleware\MiddlewareContext;

class MiddlewareContextTest extends TestCase
{
	#[Test]
	public function allCasesArePresent()
	{
		$expected = [
			MiddlewareContext::BEFORE_ROUTING,
			MiddlewareContext::BEFORE_DISPATCHING,
			MiddlewareContext::BEFORE_DISPATCHING_RESPONSE,
		];
		$this->assertSame($expected, MiddlewareContext::cases());
	}
}

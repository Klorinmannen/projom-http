<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Method;

class MethodTest extends TestCase
{
	#[Test]
	public function allCasesArePresent()
	{
		$expected = [
			'GET',
			'POST',
			'PUT',
			'PATCH',
			'DELETE',
			'HEAD',
			'OPTIONS',
			'TRACE',
			'CONNECT',
		];
		$actual = array_map(fn($case) => $case->value, Method::cases());
		$this->assertSame($expected, $actual);
	}

	#[Test]
	public function canInstantiateFromValue()
	{
		$this->assertSame(Method::GET, Method::from('GET'));
		$this->assertSame(Method::POST, Method::from('POST'));
		$this->assertSame(Method::PUT, Method::from('PUT'));
		$this->assertSame(Method::PATCH, Method::from('PATCH'));
		$this->assertSame(Method::DELETE, Method::from('DELETE'));
		$this->assertSame(Method::HEAD, Method::from('HEAD'));
		$this->assertSame(Method::OPTIONS, Method::from('OPTIONS'));
		$this->assertSame(Method::TRACE, Method::from('TRACE'));
		$this->assertSame(Method::CONNECT, Method::from('CONNECT'));
	}

	#[Test]
	public function fromThrowsOnInvalidValue()
	{
		$this->expectException(\ValueError::class);
		Method::from('INVALID');
	}
}

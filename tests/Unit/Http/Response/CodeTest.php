<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Response;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Response\Code;

class CodeTest extends TestCase
{
	#[Test]
	public function allCasesArePresent()
	{
		$expected = [
			200,
			201,
			204,
			301,
			302,
			307,
			308,
			400,
			401,
			403,
			404,
			405,
			500,
			501,
			503,
		];
		$actual = array_map(fn($case) => $case->value, Code::cases());
		$this->assertSame($expected, $actual);
	}

	#[Test]
	public function canInstantiateFromValue()
	{
		$this->assertSame(Code::OK, Code::from(200));
		$this->assertSame(Code::CREATED, Code::from(201));
		$this->assertSame(Code::NO_CONTENT, Code::from(204));
		$this->assertSame(Code::MOVED_PERMANENTLY, Code::from(301));
		$this->assertSame(Code::FOUND, Code::from(302));
		$this->assertSame(Code::TEMPORARY_REDIRECT, Code::from(307));
		$this->assertSame(Code::PERMANENT_REDIRECT, Code::from(308));
		$this->assertSame(Code::BAD_REQUEST, Code::from(400));
		$this->assertSame(Code::UNAUTHORIZED, Code::from(401));
		$this->assertSame(Code::FORBIDDEN, Code::from(403));
		$this->assertSame(Code::NOT_FOUND, Code::from(404));
		$this->assertSame(Code::METHOD_NOT_ALLOWED, Code::from(405));
		$this->assertSame(Code::INTERNAL_SERVER_ERROR, Code::from(500));
		$this->assertSame(Code::NOT_IMPLEMENTED, Code::from(501));
		$this->assertSame(Code::SERVICE_UNAVAILABLE, Code::from(503));
	}

	#[Test]
	public function fromThrowsOnInvalidValue()
	{
		$this->expectException(\ValueError::class);
		Code::from(999);
	}
}

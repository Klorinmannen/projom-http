<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Request;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request\Header;

class HeaderTest extends TestCase
{
	#[Test]
	public function getReturnsHeaderValueByName()
	{
		$header = new Header([
			'HTTP_X_FOO' => 'bar',
			'HTTP_X_BAR' => 'baz',
		]);

		$this->assertEquals('bar', $header->get('HTTP_X_FOO'));
		$this->assertEquals('baz', $header->get('X-BAR'));
		$this->assertEquals('bar', $header->get('x-foo'));
		$this->assertNull($header->get('notfound'));
	}

	#[Test]
	public function getReturnsAllHeadersWhenNoArgument()
	{
		$headers = [
			'HTTP_X_FOO' => 'bar',
			'HTTP_X_BAR' => 'baz',
		];
		$header = new Header($headers);
		$this->assertEquals($headers, $header->get());
	}

	#[Test]
	public function existsReturnsTrueIfHeaderExists()
	{
		$header = new Header(['HTTP_X_FOO' => 'bar']);
		$this->assertTrue($header->exists('X-FOO'));
		$this->assertTrue($header->exists('HTTP_X_FOO'));
		$this->assertFalse($header->exists('X-BAR'));
	}

	#[Test]
	public function setHeadersIgnoresNonHttpKeys()
	{
		$header = new Header([
			'HTTP_X_FOO' => 'bar',
			'CONTENT_TYPE' => 'application/json',
			'HTTP_X_BAR' => 'baz',
			'REQUEST_METHOD' => 'GET',
		]);

		$httpHeaders = ['HTTP_X_FOO' => 'bar',	'HTTP_X_BAR' => 'baz'];
		$this->assertEquals($httpHeaders, $header->get());
	}

	#[Test]
	public function getReturnsNullIfNoHeaders()
	{
		$header = new Header([]);
		$this->assertEquals([], $header->get());
		$this->assertNull($header->get('X-FOO'));
	}
}

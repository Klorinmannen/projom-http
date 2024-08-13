<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Response;
use Projom\Http\Api\ControllerBase;

class ControllerBaseStub extends ControllerBase 
{
	public function authorize(): bool
	{
		return true;
	}
}

class ControllerBaseTest extends TestCase
{
	#[Test]
	public function response(): void
	{
		$controller = new ControllerBaseStub([], [], '');
		
		$expected = Response::create([], 200, 'application/json');
		$actual = $controller->response();

		$this->assertEquals($expected, $actual);
	}

	#[Test]
	public function properties(): void
	{
		$controller = new ControllerBaseStub([], [], '');

		$controller->setPayload(['foo' => 'bar']);
		$controller->setStatusCode(400);
		$controller->setContentType('text/html');

		$expected = Response::create(['foo' => 'bar'], 400, 'text/html');
		$actual = $controller->response();

		$this->assertEquals($expected, $actual);
	}
}
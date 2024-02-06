<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

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
	public function test_response(): void
	{
		$controller = new ControllerBaseStub();
		
		$expected = new Response([], 200, 'application/json');
		$actual = $controller->response();

		$this->assertEquals($expected, $actual);
	}

	public function test_properties_exists(): void
	{
		$controller = new ControllerBaseStub();

		$reflection = new \ReflectionClass($controller);
		$properties = $reflection->getParentClass()->getProperties();
		$this->assertEquals(3, count($properties));

		$this->assertEquals('payload', $properties[0]->name);
		$this->assertEquals('statusCode', $properties[1]->name);
		$this->assertEquals('contentType', $properties[2]->name);
	}

	public function test_properties(): void
	{
		$controller = new ControllerBaseStub();

		$controller->setPayload(['foo' => 'bar']);
		$controller->setStatusCode(400);
		$controller->setContentType('text/html');

		$expected = new Response(['foo' => 'bar'], 400, 'text/html');
		$actual = $controller->response();

		$this->assertEquals($expected, $actual);
	}
}
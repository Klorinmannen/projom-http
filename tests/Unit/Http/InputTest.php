<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request\Input;

class InputTest extends TestCase
{
	#[Test]
	public function testCreate(): void
	{
		$input = Input::create();

		$this->assertInstanceOf(Input::class, $input);
		$this->assertIsArray($input->request);
		$this->assertIsArray($input->server);
		$this->assertIsString($input->payload);
	}

	#[Test]
	public function testConstructor(): void
	{
		$request = ['key' => 'value'];
		$server = ['HTTP_HOST' => 'localhost'];
		$files = [];
		$cookies = [];
		$payload = 'payload data';

		$input = new Input($request, $server, $files, $cookies, $payload);

		$this->assertSame($request, $input->request);
		$this->assertSame($server, $input->server);
		$this->assertSame($files, $input->files);
		$this->assertSame($cookies, $input->cookies);
		$this->assertSame($payload, $input->payload);
	}
}

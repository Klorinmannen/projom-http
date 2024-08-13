<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Input;

class InputTest extends TestCase
{
	public static function provider_get(): array
	{
		return [
			'Valid input' => [
				'request' => [
					'name' => 'John',
					'age' => 30,
					'city' => 'New York'
				],
				'key' => 'name',
				'default' => '',
				'expected' => 'John'
			],

			'Empty request data' => [
				'request' => [],
				'key' => 'name',
				'default' => '',
				'expected' => ''
			],
			
			'Default request data' => [
				'request' => [],
				'key' => 'name',
				'default' => 'Default value',
				'expected' => 'Default value'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_get')]
	public function get(array $request, string $key, string $default, string $expected): void
	{
		$input = Input::create($request, []);
		$this->assertEquals($expected, $input->get($key, $default));
	}

	#[Test]
	public function data(): void
	{
		$input = Input::create([], []);
		$this->assertEquals('', $input->data('php://input'));
	}

	public static function provider_method(): array
	{
		return [
			'Valid method' => [
				'server' => [
					'REQUEST_METHOD' => 'GET'
				],
				'expected' => 'GET'
			],

			'Empty method' => [
				'server' => [],
				'expected' => ''
			]
		];
	}

	#[Test]
	#[DataProvider('provider_method')]
	public function method(array $server, string $expected): void
	{
		$input = Input::create([], $server);
		$this->assertEquals($expected, $input->method());
	}

	public static function provider_url(): array
	{
		return [
			'Valid URL' => [
				'server' => [
					'REQUEST_URI' => 'https://example.com/path/to/resource'
				],
				'expected' => 'https://example.com/path/to/resource'
			],

			'Empty URL' => [
				'server' => [],
				'expected' => ''
			]
		];
	}

	#[Test]
	#[DataProvider('provider_url')]
	public function url(array $server, string $expected): void
	{
		$input = Input::create([], $server);
		$this->assertEquals($expected, $input->url());
	}

	public static function provider_headers(): array
	{
		return [
			'Valid headers' => [
				'server' => [
					'HTTP_HOST' => 'example.com',
					'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/12.0'
				],
				'expected' => [
					'HTTP_HOST' => 'example.com',
					'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/12.0'
				]
			],

			'Empty headers' => [
				'server' => [],
				'expected' => []
			]
		];
	}

	#[Test]
	#[DataProvider('provider_headers')]
	public function headers(array $server, array $expected): void
	{
		$input = Input::create([], $server);
		$this->assertEquals($expected, $input->headers());
	}
}

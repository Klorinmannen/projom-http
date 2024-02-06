<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Input;

class InputTest extends TestCase
{
	public static function provider_test_get(): array
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

	#[DataProvider('provider_test_get')]
	public function test_get(array $request, string $key, string $default, string $expected): void
	{
		$input = new Input($request, []);
		$this->assertEquals($expected, $input->get($key, $default));
	}

	public function test_data(): void
	{
		$input = new Input([], []);
		$this->assertEquals('', $input->data('php://input'));
	}

	public static function provider_test_method(): array
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

	#[DataProvider('provider_test_method')]
	public function test_method(array $server, string $expected): void
	{
		$input = new Input([], $server);
		$this->assertEquals($expected, $input->method());
	}

	public static function provider_test_url(): array
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

	#[DataProvider('provider_test_url')]
	public function test_url(array $server, string $expected): void
	{
		$input = new Input([], $server);
		$this->assertEquals($expected, $input->url());
	}

	public static function provider_test_headers(): array
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

	#[DataProvider('provider_test_headers')]
	public function test_headers(array $server, array $expected): void
	{
		$input = new Input([], $server);
		$this->assertEquals($expected, $input->headers());
	}
}

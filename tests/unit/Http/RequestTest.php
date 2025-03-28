<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Projom\Http\Method;
use Projom\Http\Request\Input;
use Projom\Http\Request;

class RequestTest extends TestCase
{
	public static function provider_empty(): array
	{
		return [
			'Valid' => [
				'uri' => 'https://example.com/api/v1/users/123',
				'expected' => false
			],

			'Empty' => [
				'uri' => '',
				'expected' => true
			]
		];
	}

	#[Test]
	#[DataProvider('provider_empty')]
	public function empty(string $uri, bool $expected): void
	{
		$input = new Input([], ['REQUEST_URI' => $uri], '');
		$request = Request::create($input);
		$this->assertEquals($expected, $request->empty());
	}

	public static function provider_headers(): array
	{
		return [
			'Valid' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => 'application/json'
			],
			'Header not present' => [
				'server' => [
					'HTTP_CONNECTION' => 'keep-alive'
				],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => null
			],
			'No argument call' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => null,
				'expected' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				]
			],
			'Empty server header' => [
				'server' => [],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => null
			]
		];
	}

	#[Test]
	#[DataProvider('provider_headers')]
	public function headers(array $server, null|string $header, null|string|array $expected): void
	{
		$input = new Input([], $server, '');
		$request = Request::create($input);
		$this->assertEquals($expected, $request->headers($header));
	}

	public static function provider_payload(): array
	{
		return [
			'Valid payload' => [
				'payload' => '{"KEY": "value"}',
				'expected' => '{"KEY": "value"}',
			],
			'Empty payload' => [
				'payload' => '',
				'expected' => '',
			]
		];
	}

	#[Test]
	#[DataProvider('provider_payload')]
	public function payload(string $payload, string $expected): void
	{
		$input = new Input([], [], $payload);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->payload());
	}

	public static function provider_path(): array
	{
		return [
			'Valid path' => [
				'server' => [
					'REQUEST_URI' => 'https://example.com/api/v1/users?name=John&age=30'
				],
				'expected' => '/api/v1/users'
			],
			'Missing path' => [
				'server' => [],
				'expected' => ''
			]
		];
	}

	#[Test]
	#[DataProvider('provider_path')]
	public function path(array $server, string $expected): void
	{
		$input = new Input([], $server, '');
		$request = Request::create($input);
		$this->assertEquals($expected, $request->path());
	}

	public static function provider_method(): array
	{
		return [
			'Valid method' => [
				'server' => [
					'REQUEST_METHOD' => 'GET'
				],
				'expected' => Method::GET
			],

			'Missing method' => [
				'server' => [],
				'expected' => null
			]
		];
	}

	#[Test]
	#[DataProvider('provider_method')]
	public function method(array $server, null|Method $expected): void
	{
		$input = new Input([], $server, '');
		$request = Request::create($input);
		$this->assertEquals($expected, $request->method());
	}
}

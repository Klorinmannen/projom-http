<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Input;
use Projom\Http\Request;

class RequestTest extends TestCase
{
	public static function provider_parseUrl(): array
	{
		return [
			'Valid url' => [
				'url' => 'https://example.com/api/v1/users?name=John&age=30',
				'expected' => [
					'parsedUrl' => [
						'scheme' => 'https',
						'host' => 'example.com',
						'path' => '/api/v1/users',
						'query' => 'name=John&age=30'
					],
					'queryParameterList' => [
						'name' => 'John',
						'age' => '30'
					],
					'urlPath' => '/api/v1/users',
					'urlPathPartList' => [
						'api',
						'v1',
						'users'
					]
				]
			],

			'Valid url no query' => [
				'url' => 'https://example.com/api/v1/users',
				'expected' => [
					'parsedUrl' => [
						'scheme' => 'https',
						'host' => 'example.com',
						'path' => '/api/v1/users'
					],
					'queryParameterList' => [],
					'urlPath' => '/api/v1/users',
					'urlPathPartList' => [
						'api',
						'v1',
						'users'
					]
				]
			],

			'Valid url no path no query' => [
				'url' => 'https://example.com',
				'expected' => [
					'parsedUrl' => [
						'scheme' => 'https',
						'host' => 'example.com',
					],
					'queryParameterList' => [],
					'urlPath' => '',
					'urlPathPartList' => []
				]
			],

			'Empty url' => [
				'url' => '',
				'expected' => [
					'parsedUrl' => [
						'path' => ''
					],
					'queryParameterList' => [],
					'urlPath' => '',
					'urlPathPartList' => []
				]
			]
		];
	}

	#[Test]
	#[DataProvider('provider_parseUrl')]
	public function parseUrl(string $url, array $expected): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);
		$request->parseUrl($url);
		$this->assertEquals($expected['parsedUrl'], $request->parsedUrl());
		$this->assertEquals($expected['queryParameterList'], $request->queryParameterList());
		$this->assertEquals($expected['urlPath'], $request->urlPath());
		$this->assertEquals($expected['urlPathPartList'], $request->urlPathPartList());
	}

	public static function provider_matchPattern(): array
	{
		return [
			'Valid pattern' => [
				'url' => 'https://example.com/api/v1/users/123',
				'pattern' => '/^\/api\/v1\/users\/\d+$/',
				'expected' => true
			],

			'Invalid pattern' => [
				'url' => 'https://example.com/api/v1/users/123',
				'pattern' => '/^\/api\/v1\/users$/',
				'expected' => false
			],

			'Empty pattern' => [
				'url' => 'https://example.com/api/v1/users/123',
				'pattern' => '',
				'expected' => false
			]
		];
	}

	#[Test]
	#[DataProvider('provider_matchPattern')]
	public function matchPattern(string $url, string $pattern, bool $expected): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);
		$request->parseUrl($url);
		$this->assertEquals($expected, $request->matchPattern($pattern));
	}

	public static function provider_empty(): array
	{
		return [
			'Valid url' => [
				'url' => 'https://example.com/api/v1/users/123',
				'expected' => false
			],

			'Empty url' => [
				'url' => '',
				'expected' => true
			]
		];
	}

	#[Test]
	#[DataProvider('provider_empty')]
	public function empty(string $url, bool $expected): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);
		$request->parseUrl($url);
		$this->assertEquals($expected, $request->empty());
	}

	public static function provider_header(): array
	{
		return [
			'Valid header' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => 'application/json'
			],

			'Invalid header' => [
				'server' => [
					'HTTP_CONNECTION' => 'keep-alive'
				],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => ''
			],

			'Empty header' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => '',
				'expected' => ''
			],

			'Empty server header' => [
				'server' => [],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => ''
			]
		];
	}

	#[Test]
	#[DataProvider('provider_header')]
	public function header(array $server, string $header, string $expected): void
	{
		$input = Input::create([], $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->header($header));
	}

	public static function provider_parseAuthHeader(): array
	{
		return [
			'Valid auth header' => [
				'server' => [
					'HTTP_AUTHORIZATION' => 'Bearer <token>'
				],
				'expected' => '<token>'
			],

			'Invalid auth header' => [
				'server' => [
					'HTTP_AUTHORIZATION' => '<token>'
				],
				'expected' => null
			],

			'Missing auth header' => [
				'server' => [],
				'expected' => null
			]
		];
	}

	#[Test]
	#[DataProvider('provider_parseAuthHeader')]
	public function parseAuthHeader(array $server, ?string $expected): void
	{
		$input = Input::create([], $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->authToken());
	}

	public static function provider_payload(): array
	{
		return [
			'Valid payload' => [
				'source' => __DIR__ . '/test_files/source_file.json',
				'expected' => '{"KEY": "value"}',
			],

			'Invalid payload' => [
				'source' =>  __DIR__ . '/test_files/file_does_not_exist.json',
				'expected' => '',
			],

			'No payload' => [
				'source' => '',
				'expected' => '',
			]
		];
	}

	#[Test]
	#[DataProvider('provider_payload')]
	public function payload(string $source, string $expected): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->payload($source));
	}

	public static function provider_url(): array
	{
		return [
			'Valid url' => [
				'server' => [
					'REQUEST_URI' => 'https://example.com/api/v1/users?name=John&age=30'
				],
				'expected' => 'https://example.com/api/v1/users?name=John&age=30'
			],

			'Missing url' => [
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
		$request = Request::create($input);
		$this->assertEquals($expected, $request->url());
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

			'Missing method' => [
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
		$request = Request::create($input);
		$this->assertEquals($expected, $request->httpMethod());
	}

	public static function provider_pathParameterList(): array
	{
		return [
			'Valid url' => [
				'server' => [
					'REQUEST_URI' => 'https://example.com/api/v1/users/123'
				],
				'pattern' => '/^\/api\/v1\/users\/\d+$/',
				'expected' => [
					'/api/v1/users/123'
				]
			],

			'Missing url' => [
				'server' => [],
				'pattern' => '/^\/api\/v1\/users\/\d+$/',
				'expected' => []
			]
		];
	}

	#[Test]
	#[DataProvider('provider_pathParameterList')]
	public function pathParameterList(array $server, string $pattern, array $expected): void
	{
		$input = Input::create([], $server);
		$request = Request::create($input);
		$request->matchPattern($pattern);
		$this->assertEquals($expected, $request->pathParameterList());
	}
}

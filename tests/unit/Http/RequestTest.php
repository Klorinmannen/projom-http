<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Input;
use Projom\Http\Request;

class RequestTest extends TestCase
{
	public static function provider_test_parseUrl(): array
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

	#[DataProvider('provider_test_parseUrl')]
	public function test_parseUrl(string $url, array $expected): void
	{
		$input = new Input([], []);
		$request = new Request($input);
		$request->parseUrl($url);
		$this->assertEquals($expected['parsedUrl'], $request->parsedUrl());
		$this->assertEquals($expected['queryParameterList'], $request->queryParameterList());
		$this->assertEquals($expected['urlPath'], $request->urlPath());
		$this->assertEquals($expected['urlPathPartList'], $request->urlPathPartList());
	}

	public static function provider_test_matchPattern(): array
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

	#[DataProvider('provider_test_matchPattern')]
	public function test_matchPattern(string $url, string $pattern, bool $expected): void
	{
		$input = new Input([], []);
		$request = new Request($input);
		$request->parseUrl($url);
		$this->assertEquals($expected, $request->matchPattern($pattern));
	}

	public static function provider_test_empty(): array
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

	#[DataProvider('provider_test_empty')]
	public function test_empty(string $url, bool $expected): void
	{
		$input = new Input([], []);
		$request = new Request($input);
		$request->parseUrl($url);
		$this->assertEquals($expected, $request->empty());
	}

	public static function provider_test_header(): array
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

	#[DataProvider('provider_test_header')]
	public function test_header(array $server, string $header, string $expected): void
	{
		$input = new Input([], $server);
		$request = new Request($input);
		$this->assertEquals($expected, $request->header($header));
	}

	public static function provider_test_parseAuthHeader(): array
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

	#[DataProvider('provider_test_parseAuthHeader')]
	public function test_parseAuthHeader(array $server, ?string $expected): void
	{
		$input = new Input([], $server);
		$request = new Request($input);
		$this->assertEquals($expected, $request->authToken());
	}

	public static function provider_test_payload(): array
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

	#[DataProvider('provider_test_payload')]
	public function test_payload(string $source, string $expected): void
	{
		$input = new Input([], []);
		$request = new Request($input);
		$this->assertEquals($expected, $request->payload($source));
	}

	public static function provider_test_url(): array
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

	#[DataProvider('provider_test_url')]
	public function test_url(array $server, string $expected): void
	{
		$input = new Input([], $server);
		$request = new Request($input);
		$this->assertEquals($expected, $request->url());
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

			'Missing method' => [
				'server' => [],
				'expected' => ''
			]
		];
	}

	#[DataProvider('provider_test_method')]
	public function test_method(array $server, string $expected): void
	{
		$input = new Input([], $server);
		$request = new Request($input);
		$this->assertEquals($expected, $request->httpMethod());
	}

	public static function provider_test_pathParameterList(): array
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

	#[DataProvider('provider_test_pathParameterList')]
	public function test_pathParameterList(array $server, string $pattern, array $expected): void
	{
		$input = new Input([], $server);
		$request = new Request($input);
		$request->matchPattern($pattern);
		$this->assertEquals($expected, $request->pathParameterList());
	}
}

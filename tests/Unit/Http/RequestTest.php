<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Request\Input;
use Projom\Http\Request\Timer;
use Projom\Http\Response\ResponseBase;

class RequestTest extends TestCase
{
	public static function ipDataProvider(): array
	{
		return [
			'withIp' => [
				[
					'REMOTE_ADDR' => '127.0.0.1'
				],
				'127.0.0.1'
			],
			'noIp' => [
				[],
				''
			]
		];
	}

	#[Test]
	#[DataProvider('ipDataProvider')]
	public function ip(array $server, string $expected): void
	{
		$input = Input::create(server: $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->ip());
	}

	public static function pathParametersDataProvider(): array
	{
		return [
			'empty' => [
				[],
				null,
				[],
				null
			],
			'all' => [
				[
					'id' => 42,
					'name' => 'foo'
				],
				null,
				[
					'id' => 42,
					'name' => 'foo'
				],
				null
			],
			'byName' => [
				[
					'id' => 42,
					'name' => 'foo'
				],
				'id',
				[
					'id' => 42,
					'name' => 'foo'
				],
				42
			],
			'notFound' => [
				[
					'id' => 42
				],
				'notfound',
				[
					'id' => 42
				],
				null
			]
		];
	}

	#[Test]
	#[DataProvider('pathParametersDataProvider')]
	public function pathParameters(array $params, null|int|string $name, array $allExpected, null|array|int|string $singleExpected): void
	{
		$request = Request::create();
		$request->setPathParameters($params);
		$this->assertEquals($allExpected, $request->pathParameters());
		if ($name !== null)
			$this->assertEquals($singleExpected, $request->pathParameters($name));
		else
			$this->assertNull($singleExpected);
	}

	public static function queryParametersDataProvider(): array
	{
		return [
			'all' => [
				[
					'foo' => 'bar',
					'baz' => 123
				],
				'',
				[
					'foo' => 'bar',
					'baz' => 123
				],
				null
			],
			'byName' => [
				[
					'foo' => 'bar',
					'baz' => 123
				],
				'foo',
				[
					'foo' => 'bar',
					'baz' => 123
				],
				'bar'
			],
			'notFound' => [
				[
					'foo' => 'bar'
				],
				'notfound',
				[
					'foo' => 'bar'
				],
				null
			]
		];
	}

	#[Test]
	#[DataProvider('queryParametersDataProvider')]
	public function queryParameters(array $params, string $name, array $allExpected, null|array|string $singleExpected): void
	{
		$query = http_build_query($params);
		$uri = $query ? 'https://example.com/api/test?' . $query : 'https://example.com/api/test';
		$input = Input::create(server: ['REQUEST_URI' => $uri]);
		$request = Request::create($input);
		$this->assertEquals($allExpected, $request->queryParameters());
		if ($name !== '')
			$this->assertEquals($singleExpected, $request->queryParameters($name));
		else
			$this->assertNull($singleExpected);
	}

	public static function varsDataProvider(): array
	{
		return [
			'all' => [
				[
					'foo' => 'bar'
				],
				null,
				null,
				[
					'foo' => 'bar'
				]
			],
			'byName' => [
				[
					'foo' => 'bar'
				],
				'foo',
				null,
				'bar'
			],
			'notFound' => [
				[
					'foo' => 'bar'
				],
				'notfound',
				null,
				null
			],
			'withDefault' => [
				[
					'foo' => 'bar'
				],
				'notfound',
				'default',
				'default'
			]
		];
	}

	#[Test]
	#[DataProvider('varsDataProvider')]
	public function vars(array $requestArr, null|string $name, null|string $default, null|array|string $expected): void
	{
		$input = Input::create(request: $requestArr);
		$request = Request::create($input);
		if ($name !== null)
			$this->assertEquals($expected, $request->vars($name, $default));
		else
			$this->assertEquals($expected, $request->vars());
	}

	public static function filesDataProvider(): array
	{
		return [
			'all' => [
				[
					'file1' => [
						'name' => 'a.txt'
					]
				],
				null,
				[
					'file1' => [
						'name' => 'a.txt'
					]
				]
			],
			'byName' => [
				[
					'file1' => [
						'name' => 'a.txt'
					]
				],
				'file1',
				[
					'name' => 'a.txt'
				]
			],
			'notFound' => [
				[
					'file1' => [
						'name' => 'a.txt'
					]
				],
				'notfound',
				null
			]
		];
	}

	#[Test]
	#[DataProvider('filesDataProvider')]
	public function files(array $filesArr, null|string $name, null|array $expected): void
	{
		$input = Input::create(files: $filesArr);
		$request = Request::create($input);
		if ($name !== null)
			$this->assertEquals($expected, $request->files($name));
		else
			$this->assertEquals($expected, $request->files());
	}

	public static function cookiesDataProvider(): array
	{
		return [
			'all' => [
				[
					'c1' => 'v1'
				],
				null,
				[
					'c1' => 'v1'
				]
			],
			'byName' => [
				[
					'c1' => 'v1'
				],
				'c1',
				'v1'
			],
			'notFound' => [
				[
					'c1' => 'v1'
				],
				'notfound',
				null
			]
		];
	}

	#[Test]
	#[DataProvider('cookiesDataProvider')]
	public function cookies(array $cookiesArr, null|string $name, null|array|string $expected): void
	{
		$input = Input::create(cookies: $cookiesArr);
		$request = Request::create($input);
		if ($name !== null)
			$this->assertEquals($expected, $request->cookies($name));
		else
			$this->assertEquals($expected, $request->cookies());
	}

	#[Test]
	public function find(): void
	{
		$input = Input::create(
			request: ['req' => 'rval'],
			server: ['REQUEST_URI' => 'https://example.com/api/test?id=99&q=qval'],
			files: ['f' => 'fval'],
			cookies: ['c' => 'cval'],
			payload: 'payload'
		);
		$request = Request::create($input);
		$request->setPathParameters(['id' => 99]);
		$this->assertEquals(99, $request->find('id'));
		$this->assertEquals('qval', $request->find('q'));
		$this->assertEquals('rval', $request->find('req'));
		$this->assertEquals('fval', $request->find('f'));
		$this->assertEquals('cval', $request->find('c'));
		$this->assertNull($request->find('notfound'));
	}

	#[Test]
	public function timer(): void
	{
		$input = Input::create();
		$request = Request::create($input);
		$this->assertInstanceOf(Timer::class, $request->timer());
	}

	#[Test]
	public function response(): void
	{
		$dummy = new class extends ResponseBase
		{
			public function __construct()
			{
				parent::__construct(200, 'OK', ['X-Test: true']);
			}
		};

		$request = Request::create();
		$this->assertNull($request->response());
		$request->setResponse($dummy);
		$this->assertSame($dummy, $request->response());
	}
	public static function emptyDataProvider(): array
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
	#[DataProvider('emptyDataProvider')]
	public function empty(string $uri, bool $expected): void
	{
		$input = Input::create(server: ['REQUEST_URI' => $uri]);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->empty());
	}

	public static function headerDataProvider(): array
	{
		return [
			'Valid 1' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'HTTP_CONTENT_TYPE',
				'expected' => 'application/json'
			],
			'Valid 2' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'Content-Type',
				'expected' => 'application/json'
			],
			'Valid 3' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'HTTP-CONTENT-TYPE',
				'expected' => 'application/json'
			],
			'Valid 4' => [
				'server' => [
					'HTTP_CONTENT_TYPE' => 'application/json'
				],
				'header' => 'CONTENT_TYPE',
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
	#[DataProvider('headerDataProvider')]
	public function header(array $server, null|string $header, null|string|array $expected): void
	{
		$input = Input::create(server: $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->headers($header));
	}

	public static function payloadDataProvider(): array
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
	#[DataProvider('payloadDataProvider')]
	public function payload(string $payload, string $expected): void
	{
		$input = Input::create(payload: $payload);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->payload());
	}

	public static function pathDataProvider(): array
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
	#[DataProvider('pathDataProvider')]
	public function path(array $server, string $expected): void
	{
		$input = Input::create(server: $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->path());
	}

	public static function methodDataProvider(): array
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
	#[DataProvider('methodDataProvider')]
	public function method(array $server, null|Method $expected): void
	{
		$input = Input::create(server: $server);
		$request = Request::create($input);
		$this->assertEquals($expected, $request->method());
	}
}

<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;
use Projom\Http\Response;
use Projom\Http\Response\Header;

class ResponseTest extends TestCase
{
	public static function provider_payload(): array
	{
		return [
			'Empty payload' => [
				'payload' => [],
				'expected' => []
			],
			'None empty payload' => [
				'payload' => [
					'foo' => 'bar',
					0 => 'zero'
				],
				'expected' => [
					'foo' => 'bar',
					0 => 'zero'
				]
			]
		];
	}

	#[Test]
	#[DataProvider('provider_payload')]
	public function payload(array $payload, array $expected): void
	{
		$response = Response::create($payload);
		$this->assertEquals($expected, $response->payload());
	}

	public static function provider_statusCode(): array
	{
		return [
			'500' => [
				'statusCode' => 500,
				'expected' => 500
			],
			'Default status code' => [
				'statusCode' => 200,
				'expected' => 200
			]
		];
	}

	#[Test]
	#[DataProvider('provider_statusCode')]
	public function statusCode(int $statusCode, int $expected): void
	{
		$response = Response::create([], $statusCode);
		$this->assertEquals($expected, $response->statusCode());
	}

	public static function provider_contentType(): array
	{
		return [
			'text/html' => [
				'contentType' => ContentType::TEXT_HTML,
				'expected' => 'text/html'
			],
			'Default content type' => [
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => 'application/json'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_contentType')]
	public function contentType(string $contentType, string $expected): void
	{
		$response = Response::create([], 200, $contentType);
		$this->assertEquals($expected, $response->contentType());
	}

	public static function provider_outputs(): array
	{
		return [
			'Empty payload' => [
				'payload' => [],
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => '[]'
			],
			'None empty payload' => [
				'payload' => [
					'foo' => 'bar',
					0 => 'zero'
				],
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => '{"foo":"bar","0":"zero"}'
			],
			'Empty payload with text/html content type' => [
				'payload' => [],
				'contentType' => ContentType::TEXT_HTML,
				'expected' => ''
			],
			'None empty payload with text/html content type' => [
				'payload' => [
					'<h1>foo</h1>',
					'<p>bar</p>',
					'<h2>fizz</h2>',
					'<p>buzz</p>',
				],
				'contentType' => ContentType::TEXT_HTML,
				'expected' => '<h1>foo</h1><p>bar</p><h2>fizz</h2><p>buzz</p>'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_outputs')]
	public function outputs(array $payload, string $contentType, string $expected): void
	{
		$response = Response::create($payload, 200, $contentType);
		$this->assertEquals($expected, $response->output());
	}

	public static function provider_header(): array
	{
		return [
			'application/json' => [
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => Header::APPLICATION_JSON
			],
			'text/html' => [
				'contentType' => ContentType::TEXT_HTML,
				'expected' => Header::TEXT_HTML
			]
		];
	}

	#[Test]
	#[DataProvider('provider_header')]
	public function header(string $contentType, string $expected): void
	{
		$response = Response::create([], 200, $contentType);
		$this->assertEquals($expected, $response->header());
	}

	public static function provider_sendAndExit(): array
	{
		return [
			'application/json' => [
				'payload' => [
					'foo' => 'bar',
					0 => 'zero'
				],
				'statusCode' => 200,
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => [
					'output' => '{"foo":"bar","0":"zero"}',
					'header' => Header::APPLICATION_JSON,
					'status_code' => 200
				]
			],
			'text/html' => [
				'payload' => [
					'<h1>foo</h1>',
					'<p>bar</p>',
					'<h2>fizz</h2>',
					'<p>buzz</p>',
				],
				'statusCode' => 200,
				'contentType' => ContentType::TEXT_HTML,
				'expected' => [
					'output' => '<h1>foo</h1><p>bar</p><h2>fizz</h2><p>buzz</p>',
					'header' => Header::TEXT_HTML,
					'status_code' => 200
				]
			]
		];
	}

	#[Test]
	#[DataProvider('provider_sendAndExit')]
	public function sendAndExit(array $payload, int $statusCode, string $contentType, array $expected): void
	{
		$response = Response::create($payload, $statusCode, $contentType);

		$this->expectOutputString($expected['output']);
		$response->send();

		$this->assertContains($expected['header'], xdebug_get_headers());
		$this->assertEquals($expected['status_code'], http_response_code());
	}
}

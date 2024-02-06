<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;
use Projom\Http\Response;
use Projom\Http\Response\Header;

class ResponseTest extends TestCase
{
	public static function provider_test_payload(): array
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

	#[DataProvider('provider_test_payload')]
	public function test_payload(
		array $payload,
		array $expected
	): void {
		$response = new Response($payload);
		$this->assertEquals($expected, $response->payload());
	}

	public static function provider_test_statusCode(): array
	{
		return [
			'500' => [
				'status_code' => 500,
				'expected' => 500
			],
			'Default status code' => [
				'status_code' => 200,
				'expected' => 200
			]
		];
	}

	#[DataProvider('provider_test_statusCode')]
	public function test_statusCode(
		int $statusCode,
		int $expected
	): void {
		$response = new Response([], $statusCode);
		$this->assertEquals($expected, $response->statusCode());
	}

	public static function provider_test_contentType(): array
	{
		return [
			'text/html' => [
				'content_type' => ContentType::TEXT_HTML,
				'expected' => 'text/html'
			],
			'Default content type' => [
				'content_type' => ContentType::APPLICATION_JSON,
				'expected' => 'application/json'
			]
		];
	}

	#[DataProvider('provider_test_contentType')]
	public function test_contentType(
		string $contentType,
		string $expected
	): void {
		$response = new Response([], 200, $contentType);
		$this->assertEquals($expected, $response->contentType());
	}

	public static function provider_test_output(): array
	{
		return [
			'Empty payload' => [
				'payload' => [],
				'content_type' => ContentType::APPLICATION_JSON,
				'expected' => '[]'
			],
			'None empty payload' => [
				'payload' => [
					'foo' => 'bar',
					0 => 'zero'
				],
				'content_type' => ContentType::APPLICATION_JSON,
				'expected' => '{"foo":"bar","0":"zero"}'
			],
			'Empty payload with text/html content type' => [
				'payload' => [],
				'content_type' => ContentType::TEXT_HTML,
				'expected' => ''
			],
			'None empty payload with text/html content type' => [
				'payload' => [
					'<h1>foo</h1>',
					'<p>bar</p>',
					'<h2>fizz</h2>',
					'<p>buzz</p>',
				],
				'content_type' => ContentType::TEXT_HTML,
				'expected' => '<h1>foo</h1><p>bar</p><h2>fizz</h2><p>buzz</p>'
			]
		];
	}

	#[DataProvider('provider_test_output')]
	public function test_output(
		array $payload,
		string $contentType,
		string $expected
	): void {
		$response = new Response($payload, 200, $contentType);
		$this->assertEquals($expected, $response->output());
	}

	public static function provider_test_header(): array
	{
		return [
			'application/json' => [
				'content_type' => ContentType::APPLICATION_JSON,
				'expected' => Header::APPLICATION_JSON
			],
			'text/html' => [
				'content_type' => ContentType::TEXT_HTML,
				'expected' => Header::TEXT_HTML
			]
		];
	}

	#[DataProvider('provider_test_header')]
	public function test_header(
		string $contentType,
		string $expected
	): void {
		$response = new Response([], 200, $contentType);
		$this->assertEquals($expected, $response->header());
	}

	public static function provider_test_sendAndExit(): array
	{
		return [
			'application/json' => [
				'payload' => [
					'foo' => 'bar',
					0 => 'zero'
				],
				'status_code' => 200,
				'content_type' => ContentType::APPLICATION_JSON,
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
				'status_code' => 200,
				'content_type' => ContentType::TEXT_HTML,
				'expected' => [
					'output' => '<h1>foo</h1><p>bar</p><h2>fizz</h2><p>buzz</p>',
					'header' => Header::TEXT_HTML,
					'status_code' => 200
				]
			]
		];
	}

	#[DataProvider('provider_test_sendAndExit')]
	public function test_sendAndExit(
		array $payload,
		int $statusCode,
		string $contentType,
		array $expected
	): void {

		$response = new Response($payload, $statusCode, $contentType);

		$this->expectOutputString($expected['output']);
		$response->send();

		$this->assertContains($expected['header'], xdebug_get_headers());
		$this->assertEquals($expected['status_code'], http_response_code());
	}
}

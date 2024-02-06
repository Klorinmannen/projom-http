<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Response;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;
use Projom\Http\Response\Data;

class DataTest extends TestCase
{
	public static function provider_test_encode(): array
	{
		return [
			'json' => [
				'data' => ['a' => 1],
				'contentType' => ContentType::APPLICATION_JSON,
				'expected' => '{"a":1}',
			],
			'html' => [
				'data' => ['<p>'],
				'contentType' => ContentType::TEXT_HTML,
				'expected' => '<p>',
			],
			'plain' => [
				'data' => ['a'],
				'contentType' => ContentType::TEXT_PLAIN,
				'expected' => 'a',
			],
			'css' => [
				'data' => ['a'],
				'contentType' => ContentType::TEXT_CSS,
				'expected' => 'a',
			],
			'javascript' => [
				'data' => ['a'],
				'contentType' => ContentType::TEXT_JAVASCRIPT,
				'expected' => 'a',
			],
			'csv' => [
				'data' => ['a'],
				'contentType' => ContentType::TEXT_CSV,
				'expected' => 'a',
			]
		];
	}

	#[DataProvider('provider_test_encode')]
	public function test_encode(
		mixed $data,
		string $contentType,
		string $expected
	): void {
		$actual = Data::encode($data, $contentType);
		$this->assertEquals($expected, $actual);
	}

	public function test_encode_exception(): void
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid content type');

		Data::encode([], 'invalid');
	}
}
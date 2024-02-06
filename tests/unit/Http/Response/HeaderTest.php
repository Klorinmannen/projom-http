<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Response;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;
use Projom\Http\Response\Header;

class HeaderTest extends TestCase
{
	public static function provider_test_convert(): array
	{
		return [
			'application/json' => [
				ContentType::APPLICATION_JSON,
				Header::APPLICATION_JSON,
			],
			'text/html' => [
				ContentType::TEXT_HTML,
				Header::TEXT_HTML,
			],
			'text/plain' => [
				ContentType::TEXT_PLAIN,
				Header::TEXT_PLAIN,
			],
			'text/css' => [
				ContentType::TEXT_CSS,
				Header::TEXT_CSS,
			],
			'text/javascript' => [
				ContentType::TEXT_JAVASCRIPT,
				Header::TEXT_JAVASCRIPT,
			],
			'text/csv' => [
				ContentType::TEXT_CSV,
				Header::TEXT_CSV,
			]
		];
	}

	#[DataProvider('provider_test_convert')]
	public function test_convert(
		string $contentType,
		string $expected
	): void {
		$actual = Header::convert($contentType);
		$this->assertEquals($expected, $actual);
	}

	public function test_convert_exception(): void
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid content type');
		Header::convert('invalid');
	}
}

<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;

class ContentTypeTest extends TestCase
{
	#[Test]
	public function allCasesArePresent()
	{
		$expected = [
			'Content-Type: application/json',
			'Content-Type: application/xml',
			'Content-Type: application/zip',
			'Content-Type: application/pdf',
			'Content-Type: text/html',
			'Content-Type: text/plain',
			'Content-Type: text/css',
			'Content-Type: text/javascript',
			'Content-Type: text/csv',
		];
		$actual = array_map(fn($case) => $case->value, ContentType::cases());
		$this->assertSame($expected, $actual);
	}

	#[Test]
	public function canInstantiateFromValue()
	{
		$this->assertSame(ContentType::APPLICATION_JSON, ContentType::from('Content-Type: application/json'));
		$this->assertSame(ContentType::APPLICATION_XML, ContentType::from('Content-Type: application/xml'));
		$this->assertSame(ContentType::APPLICATION_ZIP, ContentType::from('Content-Type: application/zip'));
		$this->assertSame(ContentType::APPLICATION_PDF, ContentType::from('Content-Type: application/pdf'));
		$this->assertSame(ContentType::TEXT_HTML, ContentType::from('Content-Type: text/html'));
		$this->assertSame(ContentType::TEXT_PLAIN, ContentType::from('Content-Type: text/plain'));
		$this->assertSame(ContentType::TEXT_CSS, ContentType::from('Content-Type: text/css'));
		$this->assertSame(ContentType::TEXT_JAVASCRIPT, ContentType::from('Content-Type: text/javascript'));
		$this->assertSame(ContentType::TEXT_CSV, ContentType::from('Content-Type: text/csv'));
	}

	#[Test]
	public function fromThrowsOnInvalidValue()
	{
		$this->expectException(\ValueError::class);
		ContentType::from('invalid');
	}
}

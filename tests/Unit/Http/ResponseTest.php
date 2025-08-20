<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\ContentType;
use Projom\Http\Response;
use Projom\Http\Response\Code;

class ResponseTest extends TestCase
{
	#[Test]
	public function json(): void
	{
		$data = ['foo' => 'bar'];
		$this->expectException(Response::class);
		$this->expectExceptionMessage(json_encode($data));
		$this->expectExceptionCode(Code::OK->value);
		try {
			Response::json($data);
		} catch (Response $e) {
			$this->assertContains(ContentType::APPLICATION_JSON->value, $e->getHeaders());
			throw $e;
		}
	}

	#[Test]
	public function text(): void
	{
		$text = 'Hello';
		$this->expectException(Response::class);
		$this->expectExceptionMessage($text);
		$this->expectExceptionCode(Code::OK->value);
		try {
			Response::text($text);
		} catch (Response $e) {
			$this->assertContains(ContentType::TEXT_PLAIN->value, $e->getHeaders());
			throw $e;
		}
	}

	#[Test]
	public function html(): void
	{
		$html = '<b>Hi</b>';
		$this->expectException(Response::class);
		$this->expectExceptionMessage($html);
		$this->expectExceptionCode(Code::OK->value);
		try {
			Response::html($html);
		} catch (Response $e) {
			$this->assertContains(ContentType::TEXT_HTML->value, $e->getHeaders());
			throw $e;
		}
	}

	#[Test]
	public function redirect(): void
	{
		$url = 'https://example.com';
		$this->expectException(Response::class);
		try {
			Response::redirect($url);
		} catch (Response $e) {
			$this->assertContains('Location: ' . $url, $e->getHeaders());
			throw $e;
		}
	}

	#[Test]
	public function ok(): void
	{
		$this->expectException(Response::class);
		$this->expectExceptionCode(Code::OK->value);
		try {
			Response::ok();
		} catch (Response $e) {
			$this->assertSame(Code::OK->value, $e->getCode());
			throw $e;
		}
	}

	#[Test]
	public function abort(): void
	{
		$this->expectException(Response::class);
		$this->expectExceptionCode(Code::INTERNAL_SERVER_ERROR->value);
		try {
			Response::abort();
		} catch (Response $e) {
			$this->assertSame(Code::INTERNAL_SERVER_ERROR->value, $e->getCode());
			throw $e;
		}
	}

	#[Test]
	public function reject(): void
	{
		$this->expectException(Response::class);
		$this->expectExceptionCode(Code::BAD_REQUEST->value);
		try {
			Response::reject();
		} catch (Response $e) {
			$this->assertSame(Code::BAD_REQUEST->value, $e->getCode());
			throw $e;
		}
	}
}

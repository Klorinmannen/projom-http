<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Projom\Http\Api\Pattern;

class PatternTest extends TestCase
{
	public static function provider_create(): array
	{
		return [
			'id parameter' => [
				'route' => '/users/{id}',
				'expected' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/'
			],
			'name parameter' => [
				'route' => '/users/{name}',
				'expected' => '/^\/users\/([a-zA-Z,]+)$/'
			],
			'bool parameter' => [
				'route' => '/users/{bool}',
				'expected' => '/^\/users\/(true|false)$/'
			],
			'invalid parameter' => [
				'route' => '/users/{int}',
				'expected' => '/^\/users\/{int}$/'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_create')]
	public function create(string $route, string $expected): void
	{
		$actual = Pattern::build($route);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_finalize(): array
	{
		return [
			'id pattern' => [
				'pattern' => '\/users\/([1-9][0-9]+|[1-9]+)',
				'expected' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/'
			],
			'name pattern' => [
				'pattern' => '\/users\/([a-zA-Z,]+)',
				'expected' => '/^\/users\/([a-zA-Z,]+)$/'
			],
			'bool pattern' => [
				'pattern' => '\/users\/(true|false)',
				'expected' => '/^\/users\/(true|false)$/'
			],
			'Empty pattern' => [
				'pattern' => '',
				'expected' => ''
			],
		];
	}

	#[Test]
	#[DataProvider('provider_finalize')]
	public function finalize(string $pattern, string $expected): void
	{
		$actual = Pattern::finalize($pattern);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_fromType(): array
	{
		return [
			'id type' => [
				'type' => 'id',
				'expected' => '/^([1-9][0-9]+|[1-9]+)$/'
			],
			'integer type' => [
				'type' => 'integer',
				'expected' => '/^([1-9][0-9]+|[1-9]+)$/'
			],
			'name type' => [
				'type' => 'name',
				'expected' => '/^([a-zA-Z,]+)$/'
			],
			'string type' => [
				'type' => 'string',
				'expected' => '/^([a-zA-Z,]+)$/'
			],
			'bool type' => [
				'type' => 'bool',
				'expected' => '/^(true|false)$/'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_fromType')]
	public function fromType(string $type, string $expected): void
	{
		$actual = Pattern::fromType($type);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_fromType_invalid(): array
	{
		return [
			[
				'type' => 'invalid',
				'expected' => ''
			]
		];
	}

	#[Test]
	#[DataProvider('provider_fromType_invalid')]
	public function fromType_invalid(string $type, string $expected): void
	{
		$actual = Pattern::fromType($type);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test(): array
	{
		return [
			'Good id' => [
				'pattern' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/',
				'route' => '/users/1',
				'expected' => true
			],
			'0 id' => [
				'pattern' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/',
				'route' => '/users/0',
				'expected' => false
			],
			'Bad id' => [
				'pattern' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/',
				'route' => '/users/1a',
				'expected' => false
			],
			'Good single name' => [
				'pattern' => '/^\/users\/([a-zA-Z,]+)$/',
				'route' => '/users/abc',
				'expected' => true
			],
			'Comma seperated name' => [
				'pattern' => '/^\/users\/([a-zA-Z,]+)$/',
				'route' => '/users/abc,def',
				'expected' => true
			],
			'Bad name' => [
				'pattern' => '/^\/users\/([a-zA-Z,]+)$/',
				'route' => '/users/abc,def1',
				'expected' => false
			],
			'Comma seperated name with empty' => [
				'pattern' => '/^\/users\/([a-zA-Z,]+)$/',
				'route' => '/users/abc,def,',
				'expected' => true
			],
			'Empty pattern' => [
				'pattern' => '',
				'route' => '/users/1',
				'expected' => false
			],
			'Empty route' => [
				'pattern' => '/^\/users\/([1-9][0-9]+|[1-9]+)$/',
				'route' => '',
				'expected' => false
			]
		];
	}

	#[Test]
	#[DataProvider('provider_test')]
	public function test(string $pattern, string $route, bool $expected): void
	{
		$actual = Pattern::test($pattern, $route);
		$this->assertEquals($expected, $actual);
	}
}

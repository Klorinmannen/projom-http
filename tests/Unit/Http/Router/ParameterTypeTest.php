<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Router;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Router\ParameterType;

class ParameterTypeTest extends TestCase
{
	#[Test]
	public function allCasesArePresent()
	{
		$expected = [
			'numeric_id',
			'id',
			'integer',
			'int',
			'string',
			'str',
			'bool',
			'name',
		];
		$actual = array_map(fn($case) => $case->value, ParameterType::cases());
		$this->assertSame($expected, $actual);
	}

	#[Test]
	public function toPatternReturnsExpectedRegex()
	{
		$cases = [
			'([1-9][0-9]*)' => ParameterType::NUMERIC_ID,
			'([1-9][0-9]*)' => ParameterType::ID,
			'(\-?[0-9]+)' => ParameterType::INTEGER,
			'(\-?[0-9]+)' => ParameterType::INT,
			'(.+)' => ParameterType::STRING,
			'(.+)' => ParameterType::STR,
			'(true|false)' => ParameterType::BOOL,
			'([0-9a-zA-Z_@,.\-\s]+)' => ParameterType::NAME
		];
		foreach ($cases as $expected => $case) {
			$this->assertSame($expected, $case->toPattern());
		}
	}
}

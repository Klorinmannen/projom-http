<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\ParameterContract;

class ParameterContractTest extends TestCase
{
	public static function provider_ParseList(): array
	{
		return [
			'Good test' => [
				'parameterContracts' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => true
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => true
					],
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => false
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => false
					]
				],
				'expected' => [
					'path' => [
						[
							'name' => 'id',
							'type' => 'integer',
							'required' => true
						],
						[
							'name' => 'name',
							'type' => 'string',
							'required' => true
						]
					],
					'query' => [
						[
							'name' => 'page',
							'type' => 'integer',
							'required' => false
						],
						[
							'name' => 'limit',
							'type' => 'integer',
							'required' => false
						]
					]
				],
			],
			'Empty' => [
				[],
				[]
			],
		];
	}

	#[Test]
	#[DataProvider('provider_ParseList')]
	public function parseList(array $parameterContracts, array $expected): void
	{
		$parameterContract = ParameterContract::create([]);
		$actual = $parameterContract->parseContracts($parameterContracts);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_parse(): array
	{
		return [
			'Good test' => [
				[
					'name' => 'id',
					'schema' => [
						'type' => 'integer'
					],
					'required' => 'true'
				],
				[
					'name' => 'id',
					'type' => 'integer',
					'required' => true
				]
			],
			'Missing required' => [
				[
					'name' => 'id',
					'schema' => [
						'type' => 'integer'
					]
				],
				[
					'name' => 'id',
					'type' => 'integer',
					'required' => true
				]
			],
			'Malformed required' => [
				[
					'name' => 'id',
					'schema' => [
						'type' => 'integer'
					],
					'required' => 'malformed_true'
				],
				[
					'name' => 'id',
					'type' => 'integer',
					'required' => true
				]
			],
			'Missing type' => [
				[
					'name' => 'id',
					'schema' => [],
					'required' => 'true'
				],
				[
					'name' => 'id',
					'type' => '',
					'required' => true
				]
			],
			'Missing name' => [
				[
					'schema' => [
						'type' => 'integer'
					],
					'required' => 'true'
				],
				[
					'name' => '',
					'type' => 'integer',
					'required' => true
				]
			],
			'Missing schema' => [
				[
					'name' => 'id',
					'required' => 'true'
				],
				[
					'name' => 'id',
					'type' => '',
					'required' => true
				]
			],
			'Empty' => [
				[],
				[
					'name' => '',
					'type' => '',
					'required' => true
				]
			],
		];
	}

	#[Test]
	#[DataProvider('provider_parse')]
	public function parse(array $parameterContract, array $expected): void
	{
		$parameterContractClass = ParameterContract::create([]);
		$actual = $parameterContractClass->parse($parameterContract);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_verifyPath(): array
	{
		return [
			'Good test' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					1,
					'John'
				],
				'expected' => true
			],
			'Missing path parameters' => [
				'parameterContract' => [],
				'inputParameters' => [
					1,
					'John'
				],
				'expected' => true
			],
			'Too many input parameters' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					1,
					'John',
					'extra'
				],
				'expected' => false
			],
			'Missing input parameters' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					1
				],
				'expected' => false
			],
			'Wrong order input parameters' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					'John',
					1
				],
				'expected' => false
			],
			'Required input parameters' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					0,
					''
				],
				'expected' => false
			],
			'Missing input parameters' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [
							'type' => 'string'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [],
				'expected' => false
			],
			'Type missing' => [
				'parameterContract' => [
					[
						'in' => 'path',
						'name' => 'id',
						'schema' => [],
						'required' => 'true'
					],
					[
						'in' => 'path',
						'name' => 'name',
						'schema' => [],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					1,
					'John'
				],
				'expected' => false
			],
			'Empty' => [
				'parameterContract' => [],
				'inputParameters' => [],
				'expected' => true
			],
		];
	}

	#[Test]
	#[DataProvider('provider_verifyPath')]
	public function verifyPath(array $parameterContract, array $inputParameters, bool $expected): void
	{
		$parameterContractClass = ParameterContract::create($parameterContract);
		$actual = $parameterContractClass->verifyPath($inputParameters);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_verifyQuery(): array
	{
		return [
			'Good test' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					]
				],
				'inputParameters' => [
					'page' => 1,
					'limit' => 10
				],
				'expected' => true
			],
			'Missing contract parameters' => [
				'parameterContract' => [],
				'inputParameters' => [
					'page' => 1,
					'limit' => 10
				],
				'expected' => true
			],
			'Too many input parameters' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					]
				],
				'inputParameters' => [
					'page' => 1,
					'limit' => 10,
					'sort' => 'desc'
				],
				'expected' => false
			],
			'Missing input parameters' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'false'
					]
				],
				'inputParameters' => [],
				'expected' => true
			],
			'Required input parameters' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					'page' => 0,
					'limit' => 0
				],
				'expected' => false
			],
			'Contract type missing' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [],
						'required' => 'true'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					'page' => 1,
					'limit' => 10
				],
				'expected' => false
			],
			'Input parameters are not part of contract' => [
				'parameterContract' => [
					[
						'in' => 'query',
						'name' => 'page',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					],
					[
						'in' => 'query',
						'name' => 'limit',
						'schema' => [
							'type' => 'integer'
						],
						'required' => 'true'
					]
				],
				'inputParameters' => [
					'page' => 1,
					'sort' => 'desc',
				],
				'expected' => false
			],
			'Empty' => [
				'parameterContract' => [],
				'inputParameters' => [],
				'expected' => true
			]
		];
	}

	#[Test]
	#[DataProvider('provider_verifyQuery')]
	public function verifyQuery(array $parameterContract, array $inputParameters, bool $expected): void
	{
		$parameterContractClass = ParameterContract::create($parameterContract);
		$actual = $parameterContractClass->verifyQuery($inputParameters);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_verify(): array
	{
		return [
			'Good test' => [
				'inputParameter' => '1',
				'parameterContractType' => 'integer',
				'expected' => true
			],
			'Empty input parameter' => [
				'inputParameter' => '',
				'parameterContractType' => 'integer',
				'expected' => false
			],
			'Empty parameter contract type' => [
				'inputParameter' => '1',
				'parameterContractType' => '',
				'expected' => false
			],
			'Wrong type' => [
				'inputParameter' => '1',
				'parameterContractType' => 'string',
				'expected' => false
			],
			'Wrong type' => [
				'inputParameter' => 'limit',
				'parameterContractType' => 'integer',
				'expected' => false
			],
		];
	}

	#[Test]
	#[DataProvider('provider_verify')]
	public function verify(string $inputParameter,	string $parameterContractType, bool $expected): void
	{
		$parameterContractClass = ParameterContract::create([]);
		$actual = $parameterContractClass->verify($inputParameter, $parameterContractType);
		$this->assertEquals($expected, $actual);
	}
}

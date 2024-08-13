<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\Path;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Response;

class PathContractTest extends TestCase
{
	public static function provider_parameters(): array
	{
		return [
			'Good test' => [
				'pathDetails' => [
					'parameters' => [
						[
							'name' => 'id',
							'in' => 'path',
							'required' => 'true',
							'schema' => [
								'type' => 'integer'
							]
						],
						[
							'name' => 'sort',
							'in' => 'query',
							'required' => 'true',
							'schema' => [
								'type' => 'string'
							]
						]
					]
				],
				'inputPathParameters' => [
					'1'
				],
				'inputQueryParameters' => [
					'sort' => 'asc'
				],
				'expected' => [
					'path_parameters' => true,
					'query_parameters' => true
				]
			]
		];
	}

	#[Test]
	#[DataProvider('provider_parameters')]
	public function parameters(array $pathDetails, array $inputPathParameters, array $inputQueryParameters, array $expected): void
	{
		$path = Path::create($pathDetails);
		$pathContract = PathContract::create($path);

		$actual = $pathContract->verifyInputPathParameters($inputPathParameters);
		$this->assertEquals($expected['path_parameters'], $actual);

		$actual = $pathContract->verifyInputQueryParameters($inputQueryParameters);
		$this->assertEquals($expected['query_parameters'], $actual);
	}

	public static function provider_payload(): array
	{
		return [
			'Good payload input' => [
				'pathDetails' => [
					'requestBody' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'payload' => '{"id": 1}',
				'expected' => true
			],
			'Bad payload input' => [
				'pathDetails' => [
					'requestBody' => [
						'content' => [
							'application/json' => []
						],
						'required' => 'true'
					],
				],
				'payload' => '',
				'expected' => false
			]
		];
	}

	#[Test]
	#[DataProvider('provider_payload')]
	public function payload(array $pathDetails, string $payload, bool $expected): void
	{
		$path = Path::create($pathDetails);
		$pathContract = PathContract::create($path);
		$actual = $pathContract->verifyInputPayload($payload);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_response(): array
	{
		return [
			'Good response' => [
				'pathDetails' => [
					'responses' => [
						'200' => [
							'content' => [
								'application/json' => []
							]
						]
					]
				],
				'response' => new Response([], 200, 'application/json',),
				'expected' => true
			]
		];
	}

	#[Test]
	#[DataProvider('provider_response')]
	public function response(array $pathDetails, Response $response, bool $expected): void
	{
		$path = Path::create($pathDetails);
		$pathContract = PathContract::create($path);
		$actual = $pathContract->verifyResponse($response->statusCode(), $response->contentType());
		$this->assertEquals($expected, $actual);
	}

	public static function provider_operation(): array
	{
		return [
			'Good operation' => [
				'pathDetails' => ['operationId' => 'projom_user_controller@get_by_id'],
				'expectedController' => 'Projom\User\Controller',
				'expectedOperation' => 'get_by_id'
			]
		];
	}

	#[Test]
	#[DataProvider('provider_operation')]
	public function operation(array $pathDetails, string $expectedController, string $expectedOperation): void
	{
		$path = Path::create($pathDetails);
		$pathContract = PathContract::create($path);

		$actual = $pathContract->operation();
		$this->assertEquals($expectedOperation, $actual);

		$actual = $pathContract->controller();
		$this->assertEquals($expectedController, $actual);
	}

	public static function provider_auth(): array
	{
		return [
			'Good auth' => [
				'pathDetails' => [
					'security' => [[]]
				],
				'expected' => true
			],
			'Bad auth' => [
				'pathDetails' => [
					'security' => []
				],
				'expected' => false
			]
		];
	}

	#[Test]
	#[DataProvider('provider_auth')]
	public function auth(array $pathDetails, bool $expected): void
	{
		$path = Path::create($pathDetails);
		$pathContract = PathContract::create($path);
		$actual = $pathContract->hasAuth();
		$this->assertEquals($expected, $actual);
	}
}

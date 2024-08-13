<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\PayloadContract;

class PayloadContractTest extends TestCase
{
	public static function provider_parse(): array
	{
		return [
			'Good test' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'expected' => [
					'type' => 'application/json',
					'required' => true
				]
			],
			'Required false' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => false
				],
				'expected' => [
					'type' => 'application/json',
					'required' => false
				]
			],
			'Required missing' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					]
				],
				'expected' => [
					'type' => 'application/json',
					'required' => true
				]
			],
			'Required bad name' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'bad'
				],
				'expected' => [
					'type' => 'application/json',
					'required' => true
				]
			],
			'Content missing' => [
				'payloadContract' => [
					'required' => true
				],
				'expected' => [
					'type' => '',
					'required' => true
				]
			],
			'Empty test' => [
				'payloadContract' => [],
				'expected' => []
			],
		];
	}

	#[Test]
	#[DataProvider('provider_parse')]
	public function parse(array $payloadContract, array $expected): void
	{
		$payloadContractClass = PayloadContract::create($payloadContract);
		$actual = $payloadContractClass->parseContracts($payloadContract);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_verify(): array
	{
		return [
			'Good test' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '{"user_id": "1"}',
				'expected' => true
			],
			'Not required' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => false
				],
				'inputPayload' => '',
				'expected' => true
			],
			'Not required with payload' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'false'
				],
				'inputPayload' => '{}',
				'expected' => true
			],
			'Required no payload' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '',
				'expected' => false
			],
			'Missing type' => [
				'payloadContract' => [
					'required' => 'true'
				],
				'inputPayload' => '{}',
				'expected' => false
			],
			'Bad type' => [
				'payloadContract' => [
					'content' => [
						'text/html' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '{}',
				'expected' => false
			],
			'Empty' => [
				'payloadContract' => [],
				'inputPayload' => '',
				'expected' => true
			]
		];
	}

	#[Test]
	#[DataProvider('provider_verify')]
	public function verify(array $payloadContract, string $inputPayload, bool $expected): void
	{
		$payloadContractClass = PayloadContract::create($payloadContract);
		$actual = $payloadContractClass->verify($inputPayload);
		$this->assertEquals($expected, $actual);
	}
}

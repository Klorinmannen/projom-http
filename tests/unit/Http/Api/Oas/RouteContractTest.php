<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request;
use Projom\Http\Api\ControllerBase;
use Projom\Http\Api\Oas\RouteContract;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Response;

class UserController extends ControllerBase
{
	public function authorize(): bool
	{
		return true;
	}

	public function get_by_id(Request $request): void
	{
		return;
	}
}

class RouteContractTest extends TestCase
{
	public function test_match(): void
	{
		$pathContract = $this->createStub(PathContract::class);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		// Test match
		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);

		$expected = true;
		$actual = $routeContract->match($request);
		$this->assertEquals($expected, $actual);

		// Test no pattern match
		$request = static::createStub(Request::class);
		$request->method('matchPattern')->willReturn(false);
		$expected = false;
		$actual = $routeContract->match($request);
		$this->assertEquals($expected, $actual);

		// Test no contract for method used
		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('POST');
		$request->method('matchPattern')->willReturn(true);
		$expected = false;
		$actual = $routeContract->match($request);
		$this->assertEquals($expected, $actual);
	}

	public function test_verifyInputData(): void
	{

		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('verifyPathParameters')->willReturn(true);
		$pathContract->method('verifyQueryParameters')->willReturn(true);
		$pathContract->method('verifyPayload')->willReturn(true);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/{id}/Resource/{name}',
			'\\Path\\To\\Resource\\Controller'
		);

		// Test match
		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);
		$request->method('pathParameterList')->willReturn([
			101,
			'john'
		]);
		$request->method('queryParameterList')->willReturn([
			'sort' => 'asc'
		]);
		$request->method('payload')->willReturn('{"name":"john", "user_id": 101}');

		$routeContract->match($request);

		$expected = true;
		$actual = $routeContract->verifyInputData($request);
		$this->assertEquals($expected, $actual);

		// Test empty, no inputs
		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);
		$request->method('pathParameterList')->willReturn([]);
		$request->method('queryParameterList')->willReturn([]);
		$request->method('payload')->willReturn('');

		$routeContract->match($request);

		$expected = true;
		$actual = $routeContract->verifyInputData($request);
		$this->assertEquals($expected, $actual);
	}

	public function test_verifyInputData_fail_pathParameters(): void
	{
		// Failing path parameters
		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('verifyPathParameters')->willReturn(false);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);
		$request->method('pathParameterList')->willReturn([
			101,
			'john'
		]);

		$routeContract->match($request);

		$expected = false;
		$actual = $routeContract->verifyInputData($request);
		$this->assertEquals($expected, $actual);
	}

	public function test_verifyInputData_fail_queryParameters(): void
	{
		// Failing query parameters
		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('verifyQueryParameters')->willReturn(false);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);
		$request->method('queryParameterList')->willReturn([
			'sort' => 'asc'
		]);

		$routeContract->match($request);

		$expected = false;
		$actual = $routeContract->verifyInputData($request);
		$this->assertEquals($expected, $actual);
	}

	public function test_verifyInputData_fail_payload(): void
	{
		// Failing payload
		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('verifyPayload')->willReturn(false);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);
		$request->method('payload')->willReturn('{"name":"john", "user_id": 101}');

		$routeContract->match($request);

		$expected = false;
		$actual = $routeContract->verifyInputData($request);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_verifyController(): array
	{
		return [
			[
				'routeController' => UserController::class,
				'baseController' => ControllerBase::class,
				'operation' => 'get_by_id',
				'expected' => true
			],
			[
				'routeController' => 'NonExistentClass',
				'baseController' => ControllerBase::class,
				'operation' => 'get_by_id',
				'expected' => false
			],
			[
				'routeController' => UserController::class,
				'controllerBaseClass' => ControllerBase::class,
				'operation' => 'non_existent_operation',
				'expected' => false
			],
			[
				'routeController' => UserController::class,
				'controllerBaseClass' => 'NonExistentClass',
				'operation' => 'get_by_id',
				'expected' => false
			]
		];
	}

	#[DataProvider('provider_test_verifyController')]
	public function test_verifyController(
		string $routeController,
		string $baseController,
		string $operation,
		bool $expected
	): void {

		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('operation')->willReturn($operation);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			$routeController
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);

		$routeContract->match($request);

		$actual = $routeContract->verifyController($baseController);
		$this->assertEquals($expected, $actual);
	}

	public function test_verifyResponse(): void
	{
		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('verifyResponse')->willReturn(true);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);

		$routeContract->match($request);

		$response = new Response([]);

		$expected = true;
		$actual = $routeContract->verifyResponse($response);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_hasAuth(): array
	{
		return [
			[
				'hasAuth' => true,
				'expected' => true
			],
			[
				'hasAuth' => false,
				'expected' => false
			]
		];
	}

	#[DataProvider('provider_test_hasAuth')]
	public function test_hasAuth(
		bool $hasAuth,
		bool $expected
	): void {
		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('hasAuth')->willReturn($hasAuth);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);

		$routeContract->match($request);

		$actual = $routeContract->hasAuth();
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_controller(): array
	{
		return [
			[
				'routeController' => UserController::class,
				'expected' => UserController::class
			],
			[
				'routeController' => '',
				'expected' => ''
			]
		];
	}

	#[DataProvider('provider_test_controller')]
	public function test_controller(
		string $routeController,
		string $expected
	): void {

		$routeContract = new RouteContract(
			[],
			'/Path/To/Resource/{pathParameter}',
			$routeController
		);

		$actual = $routeContract->controller();
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_operation(): array
	{
		return [
			[
				'operation' => 'get_by_id',
				'expected' => 'get_by_id'
			],
			[
				'operation' => '',
				'expected' => ''
			]
		];
	}

	#[DataProvider('provider_test_operation')]
	public function test_operation(
		string $operation,
		string $expected
	): void {

		$pathContract = $this->createStub(PathContract::class);
		$pathContract->method('operation')->willReturn($operation);

		$pathContracts = [
			'GET' => $pathContract
		];

		$routeContract = new RouteContract(
			$pathContracts,
			'/Path/To/Resource/{pathParameter}',
			'\\Path\\To\\Resource\\Controller'
		);

		$request = static::createStub(Request::class);
		$request->method('httpMethod')->willReturn('GET');
		$request->method('matchPattern')->willReturn(true);

		$routeContract->match($request);

		$actual = $routeContract->operation();
		$this->assertEquals($expected, $actual);
	}
}

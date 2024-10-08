<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\ControllerBase;
use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Oas\Contract;
use Projom\Http\Api\PathContractInterface;
use Projom\Http\Api\Router;
use Projom\Http\Input;
use Projom\Http\Request;
use Projom\Http\Response;

class RouterStub_1 extends Router
{
	public function __construct() {}

	public static function dispatch(Request $request, PathContractInterface $pathContract): Response
	{
		return Response::create(['message' => 'Hej']);
	}
}

class RouterStub_2 extends Router
{
	public function __construct() {}

	public static function start(Request $request, ContractInterface $pathContract): void
	{
		return;
	}
}

class RouterTest extends TestCase
{
	#[Test]
	public function start(): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);

		$pathContract = $this->createMock(PathContractInterface::class);
		$pathContract->method('hasAuth')->willReturn(true);
		$pathContract->method('controller')->willReturn(ControllerBase::class);
		$pathContract->method('operation')->willReturn('operation');
		$pathContract->method('verifyInputPathParameters')->willReturn(true);
		$pathContract->method('verifyInputQueryParameters')->willReturn(true);
		$pathContract->method('verifyInputPayload')->willReturn(true);
		$pathContract->method('verifyController')->willReturn(true);
		$pathContract->method('verifyResponse')->willReturn(true);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($pathContract);

		$this->expectOutputString('{"message":"Hej"}');
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->start($request, $contract);
	}

	#[Test]
	public function start_404(): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn(null);

		$this->expectExceptionCode(404);
		$routerStub_2 = new RouterStub_1();
		$routerStub_2->start($request, $contract);
	}

	#[Test]
	public function start_404_1(): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);

		$pathContract = $this->createMock(PathContractInterface::class);
		$pathContract->method('verifyInputPathParameters')->willReturn(true);
		$pathContract->method('verifyInputQueryParameters')->willReturn(true);
		$pathContract->method('verifyInputPayload')->willReturn(true);
		$pathContract->method('verifyController')->willReturn(false);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($pathContract);

		$this->expectExceptionCode(404);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->start($request, $contract);
	}

	#[Test]
	public function start_400(): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);

		$pathContract = $this->createMock(PathContractInterface::class);
		$pathContract->method('verifyInputPathParameters')->willReturn(false);
		$pathContract->method('verifyController')->willReturn(true);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($pathContract);

		$this->expectExceptionCode(400);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->start($request, $contract);
	}

	#[Test]
	public function start_500(): void
	{
		$input = Input::create([], []);
		$request = Request::create($input);

		$pathContract = $this->createMock(PathContractInterface::class);
		$pathContract->method('verifyInputPathParameters')->willReturn(true);
		$pathContract->method('verifyInputQueryParameters')->willReturn(true);
		$pathContract->method('verifyInputPayload')->willReturn(true);
		$pathContract->method('verifyController')->willReturn(true);
		$pathContract->method('verifyResponse')->willReturn(false);

		$contract = $this->createMock(Contract::class);
		$contract->method('match')->willReturn($pathContract);

		$this->expectExceptionCode(500);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->start($request, $contract);
	}
}

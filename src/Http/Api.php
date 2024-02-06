<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request;
use Projom\Http\Api\Router;
use Projom\Http\Api\Oas\Contract;

class Api
{
	private Request $request;
	private Contract $contract;

	public function __construct(string $apiContractFilePath)
	{
		$this->contract = Contract::create($apiContractFilePath);
		$this->request = Request::create();
	}

	public function start(): void
	{
		Router::start($this->request, $this->contract);
	}
}

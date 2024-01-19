<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Request;
use Projom\Http\Api\Router;
use Projom\Http\Api\ContractInterface;

class ApiService
{
	private Router $router;
	private Request $request;
	private ContractInterface $contract;

	public function __construct(
		Router $router,
		Request $request,
		ContractInterface $contract
	) {
		$this->router = $router;
		$this->request = $request;
		$this->contract = $contract;
	}

	public function startRouter(): void
	{
		$this->router->start($this->request, $this->contract);
	}
}

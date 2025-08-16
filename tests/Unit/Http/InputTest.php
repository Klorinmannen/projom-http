<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request\Input;

class InputTest extends TestCase
{
	#[Test]
	public function create(): void
	{
		$input = Input::create();

		$this->assertIsArray($input->request);
		$this->assertIsArray($input->server);
		$this->assertIsArray($input->files);
		$this->assertIsArray($input->cookies);
		$this->assertIsString($input->payload);
	}
}

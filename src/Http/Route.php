<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Route\Data;
use Projom\Http\Route\Handler;

interface Route
{
	public function get(null|Handler $handler = null): Data;
	public function post(null|Handler $handler = null): Data;
	public function put(null|Handler $handler = null): Data;
	public function delete(null|Handler $handler = null): Data;
	public function group(array $methods, null|Handler $handler = null): void;
}

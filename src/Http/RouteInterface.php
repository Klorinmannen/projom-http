<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Route\DataInterface;
use Projom\Http\Route\Handler;

interface RouteInterface
{
	public function get(null|Handler $handler = null): DataInterface;
	public function post(null|Handler $handler = null): DataInterface;
	public function put(null|Handler $handler = null): DataInterface;
	public function delete(null|Handler $handler = null): DataInterface;
	public function group(array $methods): void;
}

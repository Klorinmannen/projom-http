<?php

declare(strict_types=1);

namespace Projom\Http;

enum Method: string
{
	case GET = 'GET';
	case POST = 'POST';
	case PUT = 'PUT';
	case PATCH = 'PATCH';
	case DELETE = 'DELETE';
	case HEAD = 'HEAD';
	case OPTIONS = 'OPTIONS';
	case TRACE = 'TRACE';
	case CONNECT = 'CONNECT';
}
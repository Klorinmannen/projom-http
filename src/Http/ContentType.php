<?php

declare(strict_types=1);

namespace Projom\Http;

enum ContentType: string
{
	case APPLICATION_JSON = 'Content-Type: application/json';
	case APPLICATION_XML = 'Content-Type: application/xml';
	case APPLICATION_ZIP = 'Content-Type: application/zip';
	case APPLICATION_PDF = 'Content-Type: application/pdf';
	case TEXT_HTML = 'Content-Type: text/html';
	case TEXT_PLAIN = 'Content-Type: text/plain';
	case TEXT_CSS = 'Content-Type: text/css';
	case TEXT_JAVASCRIPT = 'Content-Type: text/javascript';
	case TEXT_CSV = 'Content-Type: text/csv';
}

<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Header;

class HeaderTest extends TestCase
{
    public static function provider_test_parseAuthHeader(): array
    {
        return [
            'Valid Bearer token' => [
                'Bearer <token>',
                '<token>',
            ],
            'Valid Bearer token with spaces' => [
                'Bearer   <token>  ',
                '<token>',
            ],
            'Invalid header format' => [
                'Basic <token>',
                null,
            ],
            'Missing Bearer keyword' => [
                'Token <token>',
                null,
            ],
            'Empty header' => [
                '',
                null,
            ],
        ];
    }

    #[DataProvider('provider_test_parseAuthHeader')]
    public function test_parseAuthHeader(string $authHeader, ?string $expected): void
    {
        $this->assertEquals($expected, Header::parseAuthHeader($authHeader));
    }
}

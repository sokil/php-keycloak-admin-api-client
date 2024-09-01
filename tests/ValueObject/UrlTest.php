<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @dataProvider isValidTestDataProvider
     */
    public function testIsValid(string $url, bool $expectedResult): void
    {
        self::assertSame($expectedResult, Url::isValid($url), \sprintf('Failed for %s', $url));
    }

    public static function isValidTestDataProvider(): array
    {
        return [
            [
                'url' => '',
                'expectedResult' => false,
            ],
            [
                'url' => 'site',
                'expectedResult' => false,
            ],
            [
                'url' => 'http',
                'expectedResult' => false,
            ],
            [
                'url' => 'https:/site.com',
                'expectedResult' => false,
            ],
            [
                'url' => 'https://',
                'expectedResult' => false,
            ],
            [
                'url' => 'site.com',
                'expectedResult' => false,
            ],
            [
                'url' => 'localhost',
                'expectedResult' => false,
            ],
            [
                'url' => 'http://site',
                'expectedResult' => true,
            ],
            [
                'url' => 'http://.com',
                'expectedResult' => false,
            ],
            [
                'url' => 'https://site.',
                'expectedResult' => true,
            ],
            [
                'url' => 'https://user@site.com:8888/path?query[]=some_param#fragment1',
                'expectedResult' => true,
            ],
        ];
    }

    public function testGetExceptionForInvalidUrl(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Url('');
    }

    public function testCorrectValue(): void
    {
        $urlScalar = 'https://user@site.com:8888/path?query[]=some_param#fragment1';

        $vo = new Url($urlScalar);

        self::assertSame($urlScalar, $vo->value);
        self::assertSame($urlScalar, (string) $vo);
    }
}

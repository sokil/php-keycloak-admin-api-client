<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestValid
     */
    public function testValid(string $email)
    {
        $emailObj = new Email($email);

        $this->assertEquals(strtolower($email), $emailObj->getValue());
    }

    public static function dataProviderTestValid(): array
    {
        return [
            ['test@test.com'],
            ['test.test@test.com'],
            ['test.test.test@test.com'],
            ['test@test.test.com'],
            ['test+test@test.com'],
        ];
    }

    /**
     * @dataProvider dataProviderTestInvalid
     */
    public function testInvalid(string $email)
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email($email);
    }

    public static function dataProviderTestInvalid(): array
    {
        return [
            ['TEST@test.com'], // email must be lowercase
            ['test'],
            ['@test.com'],
            ['test@'],
        ];
    }

    public static function fromStringDataProvider()
    {
        return [
            ['test@test.com'],
            ['test.test@test.com'],
            ['test.test.test@test.com'],
            ['test@test.test.com'],
            ['test+test@test.com'],
            ['TEST@test.com'],
        ];
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $value)
    {
        $this->assertInstanceOf(Email::class, Email::fromString($value));
    }
}

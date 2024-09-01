<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testBuildFromRfc4122String()
    {
        $expectedUuid = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');

        $uuid = Uuid::fromString($expectedUuid->toRfc4122());
        $this->assertEquals($expectedUuid->toRfc4122(), $uuid->toRfc4122());

        $this->assertSame(7, $uuid->getVersion());
    }

    public function testBuildFromBinaryString()
    {
        $expectedUuid = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');

        $uuid = Uuid::fromString($expectedUuid->toBinary());
        $this->assertEquals($expectedUuid->toRfc4122(), $uuid->toRfc4122());

        $this->assertSame(7, $uuid->getVersion());
    }

    public function testBuildFromHexadecimalString()
    {
        $expectedUuid = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');

        $uuid = Uuid::fromString($expectedUuid->toHexadecimal());
        $this->assertEquals($expectedUuid->toRfc4122(), $uuid->toRfc4122());

        $this->assertSame(7, $uuid->getVersion());
    }

    public function testEquals()
    {
        $uuid1 = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');
        $uuid2 = new Uuid('1191adbc-5ae8-71ce-acb5-15571046f5c5');
        $uuid3 = Uuid::fromString($uuid1->toBinary());

        $this->assertTrue($uuid1->equals($uuid3));
        $this->assertFalse($uuid1->equals($uuid2));
    }

    public static function isValidPositiveDataProvider()
    {
        return [
            [hex2bin('00112233445566778899AABBCCDDEEFF')],
            ['00112233445566778899AABBCCDDEEFF'],
            ['00112233-4455-6677-8899-AABBCCDDEEFF'],
            ['00000000-0000-0000-0000-000000000000'],
        ];
    }

    /**
     * @dataProvider isValidPositiveDataProvider
     */
    public function testIsValidPositive(string $uuid)
    {
        $this->assertTrue(Uuid::isValid($uuid));
    }

    public static function isValidNegativeDataProvider()
    {
        return [
            [hex2bin('00112233445566778899AABBCCDDEE')],
            ['00112233445566778899AABBCCDDEE'],
            ['00112233-4455-6677-8899-AABBCCDDEE'],
            ['00112233445566778899-AABBCCDDEEFF'],
            [str_repeat('-', 36)],
        ];
    }

    /**
     * @dataProvider isValidNegativeDataProvider
     */
    public function testIsValidNegative(string $uuid)
    {
        $this->assertFalse(Uuid::isValid($uuid));
    }
}

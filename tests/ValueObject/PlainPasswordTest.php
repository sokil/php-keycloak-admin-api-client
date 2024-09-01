<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

use PHPUnit\Framework\TestCase;

class PlainPasswordTest extends TestCase
{
    public function testBuildRandom()
    {
        $password = PlainPassword::buildRandom(7);
        $this->assertSame(7, strlen((string) $password));
    }

    public function testConstructor()
    {
        $string = 'somestr-#@$';

        $password = new PlainPassword($string);
        $this->assertEquals($string, (string) $password);
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Service;

use PHPUnit\Framework\TestCase;

class DateTimeFunctionsTest extends TestCase
{
    public function testTimestampWithMillisecondsToDateTime()
    {
        $timestampWithMilliseconds = 1661173941347;
        $rfc3339ExtendedDateTime = '2022-08-22T13:12:21.347+00:00';

        $dateTime = DateTimeFunctions::timestampWithMillisecondsToDateTime($timestampWithMilliseconds);
        $this->assertSame(
            $rfc3339ExtendedDateTime,
            $dateTime->format(\DateTimeImmutable::RFC3339_EXTENDED)
        );
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Service;

class DateTimeFunctions
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function timestampWithMillisecondsToDateTime(int $timestampWithMilliseconds): \DateTimeImmutable
    {
        $timestampWithMilliseconds = (string) $timestampWithMilliseconds;

        $dateTime = \DateTimeImmutable::createFromFormat(
            'U.v',
            substr($timestampWithMilliseconds, 0, -3) . '.' . substr($timestampWithMilliseconds, -3)
        );

        if (!$dateTime) {
            throw new \InvalidArgumentException('Invalid timestamp passed');
        }

        return $dateTime;
    }
}

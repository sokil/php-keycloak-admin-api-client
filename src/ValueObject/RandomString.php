<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

/**
 * @psalm-immutable
 */
final class RandomString implements \Stringable
{
    private function __construct(
        public readonly string $value,
    ) {
    }

    public static function buildAscii(int $length): self
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+]}[{;:,<.>/?\'"\|';

        return self::buildByAlphabet($length, $alphabet);
    }

    public static function buildNumeric(int $length): self
    {
        $alphabet = '1234567890';

        return self::buildByAlphabet($length, $alphabet);
    }

    public static function buildByAlphabet(int $length, string $alphabet): self
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('value length must be positive');
        }

        $alphabetLength = strlen($alphabet);
        if ($alphabetLength <= 0) {
            throw new \InvalidArgumentException('alphabet length must be positive');
        }

        $value = '';
        $maxIndex = $alphabetLength - 1;

        for ($i = 0; $i < $length; $i++) {
            $value .= $alphabet[\random_int(0, $maxIndex)];
        }

        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

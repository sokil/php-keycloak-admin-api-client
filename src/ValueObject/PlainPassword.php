<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

/**
 * @psalm-immutable
 */
final class PlainPassword implements \Stringable
{
    public const MIN_LENGTH = 5;
    public const MAX_LENGTH = 4096;

    public function __construct(
        public readonly string $value,
    ) {
        $valueLength = strlen($value);

        if ($valueLength < self::MIN_LENGTH) {
            throw new \OutOfRangeException('value length too small');
        }

        if ($valueLength > self::MAX_LENGTH) {
            throw new \OutOfRangeException('value length too big');
        }
    }

    public static function buildRandom(int $length): self
    {
        return new self(RandomString::buildAscii($length)->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

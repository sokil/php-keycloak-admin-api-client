<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

final class Email implements \Stringable
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        public readonly string $value
    ) {
        if (!self::isValid($this->value)) {
            throw new \InvalidArgumentException('value must be valid email');
        }
    }

    /**
     * @deprecated Use public property
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Value will be converted to lowercase
     */
    public static function fromString(string $value): self
    {
        $value = strtolower($value);

        return new self($value);
    }

    /**
     * Value will be converted to lowercase
     */
    public static function tryFromString(string $value): ?self
    {
        try {
            return self::fromString($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Email must be in lower case
     */
    public static function isValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            && strtolower($email) === $email;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

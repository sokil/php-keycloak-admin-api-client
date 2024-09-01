<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

/**
 * Simple wrapper for url string without any additional validation except the native filter_var()
 *
 * Url is always absolute and it MUST include scheme
 */
class Url implements \Stringable
{
    public function __construct(
        public readonly string $value
    ) {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid url');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function isValid(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $validatedUrl = \filter_var($url, FILTER_VALIDATE_URL);
        $sanitizedUrl = \filter_var($url, FILTER_SANITIZE_URL);

        if (!$validatedUrl || $sanitizedUrl !== $url) {
            return false;
        }

        return true;
    }
}

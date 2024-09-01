<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

class Uuid implements \Stringable, \JsonSerializable
{
    public const UUID_RFC_4122_PATTERN = '/^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$/Di';

    public const NIL_UUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Canonical RFC 4122 (dashed 36-bytes) form
     *
     * @var string
     */
    private string $value;

    /**
     * @var int|null Null for nil UUID
     */
    private ?int $version = null;

    /**
     * @param string $value Canonical RFC 4122 (dashed 36-bytes) form
     */
    final public function __construct(string $value)
    {
        if ($value !== self::NIL_UUID) {
            $version = (int) $value[14];

            if (!in_array($version, [1, 2, 3, 4, 5, 6, 7])) {
                throw new \InvalidArgumentException('Unknown UUID version');
            }

            if (!preg_match(self::UUID_RFC_4122_PATTERN, $value)) {
                throw new \InvalidArgumentException('Invalid UUID');
            }
        } else {
            $version = null;
        }

        $this->value = $value;
        $this->version = $version;
    }

    public static function isValid(string $uuid): bool
    {
        try {
            Uuid::fromString($uuid);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function tryFromString(string $uuid): ?Uuid
    {
        try {
            return Uuid::fromString($uuid);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Creates new UUID object using provided value
     *
     * Following values allowed as argument:
     * - 16 bytes string - resolves as raw binary UUID
     * - 32 bytes string - resolves as hexadecimal UUID without dashes
     * - 36 bytes string - resolves as hexadecimal UUID with dashes
     */
    public static function fromString(string $uuid): Uuid
    {
        switch (\strlen($uuid)) {
            case 16:
                $uuid = self::convertBinaryToHexadecimal($uuid);
                $uuid = self::convertHexadecimalToRfc4122($uuid);
                break;
            case 32:
                $uuid = self::convertHexadecimalToRfc4122($uuid);
                break;
            case 36:
                break;
            default:
                throw new \InvalidArgumentException(
                    'Invalid UUID length, may be 16 bytes for binary, 32 for hexadecimal and 36 for dashed form'
                );
        }

        return new static($uuid);
    }

    /**
     * @return bool
     */
    public function equals(Uuid $other): bool
    {
        return $other->value === $this->value;
    }

    private static function convertHexadecimalToRfc4122(string $uuid): string
    {
        $uuid = substr_replace($uuid, '-', 8, 0);
        $uuid = substr_replace($uuid, '-', 13, 0);
        $uuid = substr_replace($uuid, '-', 18, 0);
        $uuid = substr_replace($uuid, '-', 23, 0);

        return $uuid;
    }

    private static function convertBinaryToHexadecimal(string $binaryUuid): string
    {
        return bin2hex($binaryUuid);
    }

    /**
     * @return int|null
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * Dashed 36-bytes string in RFC 4122
     */
    public function toRfc4122(): string
    {
        return $this->value;
    }

    /**
     * Returns hexadecimal representation of UUID witho
     *
     * @return string
     */
    public function toHexadecimal(): string
    {
        return str_replace('-', '', $this->value);
    }

    /**
     * Returns "raw" binary string
     *
     * @return string
     */
    public function toBinary(): string
    {
        $bin = \hex2bin($this->toHexadecimal());

        return $bin;
    }

    /**
     * Return UUID in RFC 4122 format
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Return UUID in RFC 4122 format
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    private static function format(string $uuid, string $version): string
    {
        $uuid[8] = $uuid[8] & "\x3F" | "\x80";
        $uuid = substr_replace(bin2hex($uuid), '-', 8, 0);
        $uuid = substr_replace($uuid, $version, 13, 1);
        $uuid = substr_replace($uuid, '-', 18, 0);

        return substr_replace($uuid, '-', 23, 0);
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Representation;

use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;

final class Role
{
    /**
     * @param array<string, array<int, string>> $attributes May be empty if brief roles requested
     */
    public function __construct(
        public readonly Uuid $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly bool $composite,
        public readonly bool $clientRole,
        public readonly array $attributes = []
    ) {
    }

    public static function fromScalarMap(array $scalarMap): self
    {
        /** @var array<string, array<int, string>> $attributes */
        $attributes = $scalarMap['attributes'] ?? [];

        return new self(
            Uuid::fromString((string) $scalarMap['id']),
            (string) $scalarMap['name'],
            isset($scalarMap['description']) ? (string) $scalarMap['description'] : null,
            (bool) $scalarMap['composite'],
            (bool) $scalarMap['clientRole'],
            $attributes
        );
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Representation;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\AbstractImmutableGenericCollection;

/**
 * @extends AbstractImmutableGenericCollection<Role>
 */
class RoleCollection extends AbstractImmutableGenericCollection
{
    protected function getType(): string
    {
        return Role::class;
    }

    public static function fromScalarMaps(array $maps): self
    {
        return new self(
            array_map(
                static fn (array $map) => Role::fromScalarMap($map),
                $maps
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Representation;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\AbstractImmutableGenericCollection;

/**
 * @extends AbstractImmutableGenericCollection<Credential>
 */
class CredentialCollection extends AbstractImmutableGenericCollection
{
    protected function getType(): string
    {
        return Credential::class;
    }

    /**
     * @param array<array{type: string, value: string, temporary: bool}> $maps
     */
    public static function fromScalarMaps(array $maps): self
    {
        return new self(
            array_map(
                static fn (array $map) => Credential::fromScalarMap($map),
                $maps
            )
        );
    }

    /**
     * @return array<array{type: string, value: string, temporary: bool}>
     */
    public function toScalarMaps(): array
    {
        /** @var array<array{type: string, value: string, temporary: bool}> $maps */
        $maps = $this->map(
            static fn (Credential $credential) => $credential->toScalarMap()
        );

        return $maps;
    }
}

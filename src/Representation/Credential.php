<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Representation;

use Sokil\KeycloakAdminApiClient\ValueObject\CredentialType;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;

final class Credential
{
    public function __construct(
        public readonly CredentialType $credentialType,
        public readonly PlainPassword $plainPassword,
        public readonly bool $temporary,
    ) {
    }

    /**
     * @param array{type: string, value: string, temporary: bool} $map
     */
    public static function fromScalarMap(array $map): self
    {
        return new self(
            CredentialType::from($map['type']),
            new PlainPassword($map['value']),
            (bool) $map['temporary'],
        );
    }

    /**
     * @return array{type: string, value: string, temporary: bool}
     */
    public function toScalarMap(): array
    {
        return [
            'type' => $this->credentialType->value,
            'value' => $this->plainPassword->value,
            'temporary' => $this->temporary,
        ];
    }
}

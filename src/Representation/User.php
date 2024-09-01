<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Representation;

use Sokil\KeycloakAdminApiClient\Service\DateTimeFunctions;
use Sokil\KeycloakAdminApiClient\ValueObject\UserAfterRegistrationAction;
use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;

final class User
{
    /**
     * @param array<string, array<int, mixed>> $attributes
     * @param UserAfterRegistrationAction[] $requiredActions
     */
    public function __construct(
        public readonly Uuid $userId,
        public readonly \DateTimeImmutable $createdAt,
        public readonly string $username,
        public readonly bool $enabled,
        public readonly bool $totp,
        public readonly bool $emailVerified,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly Email $email,
        public readonly array $attributes,
        public readonly array $requiredActions,
    ) {
    }

    public static function fromScalarMap(array $scalarMap): self
    {
        /** @var array<string, array<int, mixed>> $attributes */
        $attributes = $scalarMap['attributes'];

        return new self(
            Uuid::fromString((string) $scalarMap['id']),
            DateTimeFunctions::timestampWithMillisecondsToDateTime((int) $scalarMap['createdTimestamp']),
            (string) $scalarMap['username'],
            (bool) $scalarMap['enabled'],
            (bool) $scalarMap['totp'],
            (bool) $scalarMap['emailVerified'],
            (string) $scalarMap['firstName'],
            (string) $scalarMap['lastName'],
            Email::fromString((string) $scalarMap['email']),
            $attributes,
            !empty($scalarMap['requiredActions'])
                ? array_map(
                    fn (string $action) => UserAfterRegistrationAction::from($action),
                    (array) $scalarMap['requiredActions']
                )
                : [],
        );
    }
}

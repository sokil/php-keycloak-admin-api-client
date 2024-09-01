<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

enum CredentialType: string
{
    case Password = 'password';
}

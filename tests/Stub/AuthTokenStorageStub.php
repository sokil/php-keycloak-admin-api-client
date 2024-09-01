<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Stub;

use Sokil\RestApiClient\Auth\OAuth2\AuthTokenStorage\AuthTokenStorageInterface;

class AuthTokenStorageStub implements AuthTokenStorageInterface
{
    public function get(string $key): string
    {
        return 'some-token';
    }

    public function set(string $key, string $value, int $expireTtl): bool
    {
        return true;
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Stub;

use Sokil\KeycloakAdminApiClient\KeycloakClientFactory;
use Sokil\RestApiClient\RestApiClient;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\ResponseInterface;

class KeycloakClientBuilder
{
    public static function build(callable|iterable|ResponseInterface $responseFactory = null): RestApiClient
    {
        $mockHttpClient = new MockHttpClient($responseFactory);
        $httpClient = new Psr18Client($mockHttpClient);

        $clientFactory = new KeycloakClientFactory(
            $httpClient,
            $httpClient,
            $httpClient,
            new AuthTokenStorageStub(),
            new NullLogger(),
        );

        $client = $clientFactory->build(
            'http://keycloak/admin/realms/some-realm',
            'http://keycloak/realms/[[realm]]/protocol/openid-connect/token',
            'some-client-id',
            'some-client-secret',
        );

        return $client;
    }
}

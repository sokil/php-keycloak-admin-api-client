<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class SendVerifyEmailRequestTest extends TestCase
{
    public function testPositive()
    {
        $userId = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $userId,
            ) {
                $this->assertSame(
                    'http://keycloak/admin/realms/some-realm/users/'
                        . $userId->toRfc4122()
                        . '/send-verify-email?client_id=some_client&redirect_uri=https%3A%2F%2Fserver.com',
                    $url
                );

                return new MockResponse(
                    '',
                    [
                        'http_code' => 204,
                    ]
                );
            }
        );

        $client->call(
            new SendVerifyEmailRequest(
                $userId,
                'some_client',
                new Url('https://server.com')
            )
        );
    }
}

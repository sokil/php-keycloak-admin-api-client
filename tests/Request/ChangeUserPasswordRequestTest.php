<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ChangeUserPasswordRequestTest extends TestCase
{
    public function testPositive()
    {
        $userId = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');
        $password = new PlainPassword('some-pass');

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $userId,
                $password,
            ) {
                $this->assertSame(
                    sprintf(
                        'http://keycloak/admin/realms/some-realm/users/%s/reset-password',
                        $userId->toRfc4122(),
                    ),
                    $url
                );

                $this->assertSame(
                    \json_encode([
                        "type" => "password",
                        "value" => $password->value,
                        "temporary" => false,
                    ]),
                    $options['body']
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
            new ChangeUserPasswordRequest(
                $userId,
                $password,
            )
        );
    }
}

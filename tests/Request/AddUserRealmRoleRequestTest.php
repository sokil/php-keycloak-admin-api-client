<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class AddUserRealmRoleRequestTest extends TestCase
{
    public function testPositive()
    {
        $userId = new Uuid('0191adbc-5ae8-71ce-acb5-15571046f5c4');
        $roleId = new Uuid('1291adbc-5ae8-71ce-acb5-15571046f589');
        $roleName = 'my-cool-role';

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $userId,
                $roleId,
                $roleName,
            ) {
                $this->assertSame('POST', $method);

                $this->assertSame(
                    sprintf(
                        'http://keycloak/admin/realms/some-realm/users/%s/role-mappings/realm',
                        $userId->toRfc4122(),
                    ),
                    $url
                );

                $this->assertSame(
                    \json_encode(
                        [
                            [
                                'id' => $roleId->toRfc4122(),
                                'name' => $roleName,
                            ]
                        ]
                    ),
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
            new AddUserRealmRoleRequest(
                $userId,
                $roleId,
                $roleName,
            )
        );
    }
}

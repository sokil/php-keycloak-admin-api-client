<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Representation\Credential;
use Sokil\KeycloakAdminApiClient\Representation\CredentialCollection;
use Sokil\KeycloakAdminApiClient\Representation\User;
use Sokil\KeycloakAdminApiClient\Representation\UserCollection;
use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\CredentialType;
use Sokil\KeycloakAdminApiClient\ValueObject\UserAfterRegistrationAction;
use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class GetUsersRequestTest extends TestCase
{
    public function testPositive()
    {
        $expectedUserRepresentation = [
            "id" => "92ccf7ea-bc64-48e6-ba58-792db362beda",
            "createdTimestamp" => 1661000371292,
            "username" => "testuser-11",
            "enabled" => true,
            "totp" => false,
            "emailVerified" => false,
            "firstName" => "John",
            "lastName" => "Dow",
            "email" => "testuser-11@example.com",
            "attributes" => [
                "TENANT_ID" => [
                    "42"
                ]
            ],
            "disableableCredentialTypes" => [],
            "requiredActions" => [
                "VERIFY_EMAIL"
            ],
            "notBefore" => 0,
            "access" => [
                "manageGroupMembership" => true,
                "view" => true,
                "mapRoles" => true,
                "impersonate" => false,
                "manage" => true
            ]
        ];

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $expectedUserRepresentation
            ) {
                $this->assertSame('GET', $method);

                $this->assertSame(
                    'http://keycloak/admin/realms/some-realm/users?email=testuser-11%40example.com',
                    $url
                );

                return new MockResponse(
                    \json_encode([$expectedUserRepresentation]),
                    [
                        'http_code' => StatusCodeInterface::STATUS_OK,
                        'response_headers' => [
                            'Content-type: application/json',
                        ],
                    ]
                );
            }
        );

        /** @var UserCollection $userRepresentations */
        $userRepresentations = $client->call(
            new GetUsersRequest(
                Email::fromString("testuser-11@example.com")
            )
        );

        $this->assertCount(1, $userRepresentations);

        $userRepresentation = $userRepresentations->current();

        $this->assertSame(
            $expectedUserRepresentation['id'],
            $userRepresentation->userId->toRfc4122()
        );

        $this->assertSame(
            (string) $expectedUserRepresentation['createdTimestamp'],
            $userRepresentation->createdAt->format('Uv')
        );

        $this->assertSame(
            $expectedUserRepresentation['username'],
            $userRepresentation->username
        );

        $this->assertSame(
            $expectedUserRepresentation['enabled'],
            $userRepresentation->enabled
        );

        $this->assertSame(
            $expectedUserRepresentation['totp'],
            $userRepresentation->totp
        );

        $this->assertSame(
            $expectedUserRepresentation['emailVerified'],
            $userRepresentation->emailVerified
        );

        $this->assertSame(
            $expectedUserRepresentation['firstName'],
            $userRepresentation->firstName
        );

        $this->assertSame(
            $expectedUserRepresentation['lastName'],
            $userRepresentation->lastName
        );

        $this->assertSame(
            $expectedUserRepresentation['email'],
            $userRepresentation->email->value
        );

        $this->assertSame(
            $expectedUserRepresentation['attributes'],
            $userRepresentation->attributes
        );

        $this->assertSame(
            $expectedUserRepresentation['requiredActions'],
            array_map(
                static fn (UserAfterRegistrationAction $action) => $action->value,
                $userRepresentation->requiredActions
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Representation\Credential;
use Sokil\KeycloakAdminApiClient\Representation\CredentialCollection;
use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\CredentialType;
use Sokil\KeycloakAdminApiClient\ValueObject\UserAfterRegistrationAction;
use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class CreateUserRequestTest extends TestCase
{
    public function testPositive()
    {
        $expectedResourceLocation = 'http://keycloak/users/42';

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $expectedResourceLocation
            ) {
                $this->assertSame(
                    \json_encode([
                        "username" => "username",
                        "email" => "username@example.priv",
                        "enabled" => true,
                        "emailVerified" => false,
                        "credentials" => [
                            [
                                "type" => "password",
                                "value" => "some-strong-pass",
                                "temporary" => true
                            ]
                        ],
                        "firstName" => "myFirstName",
                        "lastName" => "myLastName",
                        "groups" => ["some-group1", "some-group2"],
                        "attributes" => [
                            "some-attribute1" => "some-attribute2-value",
                            "some-attribute2" => "some-attribute2-value"
                        ],
                        "realmRoles" => ["some-realm-role1", "some-realm-role2"],
                        "requiredActions" => ["VERIFY_EMAIL"]
                    ]),
                    $options['body']
                );
                return new MockResponse(
                    '',
                    [
                        'http_code' => StatusCodeInterface::STATUS_CREATED,
                        'response_headers' => [
                            'Location: ' . $expectedResourceLocation,
                        ],
                    ]
                );
            }
        );

        /** @var Url $createdUserResourceUri */
        $createdUserResourceUri = $client->call(
            new CreateUserRequest(
                'username',
                new Email('username@example.priv'),
                true,
                false,
                new CredentialCollection([
                    new Credential(
                        CredentialType::Password,
                        new PlainPassword('some-strong-pass'),
                        true
                    )
                ]),
                'myFirstName',
                'myLastName',
                [
                    'some-group1',
                    'some-group2',
                ],
                [
                    'some-attribute1' => 'some-attribute2-value',
                    'some-attribute2' => 'some-attribute2-value',
                ],
                [
                    'some-realm-role1',
                    'some-realm-role2',
                ],
                [
                    UserAfterRegistrationAction::VerifyEmail,
                ]
            )
        );

        $this->assertSame(
            $expectedResourceLocation,
            $createdUserResourceUri->value
        );
    }
}

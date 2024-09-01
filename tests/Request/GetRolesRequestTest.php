<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Representation\Role;
use Sokil\KeycloakAdminApiClient\Representation\RoleCollection;
use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class GetRolesRequestTest extends TestCase
{
    public static function positiveDataProvider()
    {
        return [
            ['briefRepresentation' => true],
            ['briefRepresentation' => false],
        ];
    }

    /**
     * @dataProvider positiveDataProvider
     */
    public function testPositive(bool $briefRepresentation)
    {
        $expectedRoleRepresentation = [
            "id" => "2485470b-017d-4b76-a392-a7aa21024518",
            "name" => "some-role-name",
            "composite" => false,
            "clientRole" => false,
            "containerId" => "b2b",
        ];

        if (!$briefRepresentation) {
            $expectedRoleRepresentation['attributes'] = [
                "some-param" => [
                    "42"
                ]
            ];
        }

        $client = KeycloakClientBuilder::build(
            function (
                string $method,
                string $url,
                array $options
            ) use (
                $expectedRoleRepresentation,
                $briefRepresentation
            ) {
                $this->assertSame('GET', $method);

                $this->assertSame(
                    'http://keycloak/admin/realms/some-realm/roles?briefRepresentation=' . ($briefRepresentation ? "true" : "false"),
                    $url
                );

                return new MockResponse(
                    \json_encode([$expectedRoleRepresentation]),
                    [
                        'http_code' => StatusCodeInterface::STATUS_OK,
                        'response_headers' => [
                            'Content-type: application/json',
                        ],
                    ]
                );
            }
        );

        /** @var RoleCollection $roleRepresentations */
        $roleRepresentations = $client->call(
            new GetRolesRequest(
                $briefRepresentation,
            )
        );

        $this->assertCount(1, $roleRepresentations);

        /** @var Role $roleRepresentation */
        $roleRepresentation = $roleRepresentations->current();

        $this->assertSame(
            $expectedRoleRepresentation['id'],
            $roleRepresentation->id->toRfc4122(),
        );

        if (!$briefRepresentation) {
            $this->assertSame(
                $expectedRoleRepresentation['attributes'],
                $roleRepresentation->attributes
            );
        }
    }
}

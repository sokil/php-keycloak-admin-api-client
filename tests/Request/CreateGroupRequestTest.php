<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Stub\KeycloakClientBuilder;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class CreateGroupRequestTest extends TestCase
{
    public function testPositive()
    {
        $expectedResourceLocation = 'http://localhost:8080/admin/realms/b2b/groups/a278b4fe-4889-4c86-94a1-18ae2e447334';

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
                        "name" => "some-name",
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

        /** @var Url $createdGroupResourceUri */
        $createdGroupResourceUri = $client->call(
            new CreateGroupRequest("some-name")
        );

        $this->assertSame(
            $expectedResourceLocation,
            $createdGroupResourceUri->value
        );
    }
}

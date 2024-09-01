<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\GetUserException;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\UserNotExistsException;
use Sokil\KeycloakAdminApiClient\Representation\User;
use Sokil\KeycloakAdminApiClient\Representation\UserCollection;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GetUsersRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        ?Email $email
    ) {
        $queryString = http_build_query(
            array_filter([
                'email' => $email?->value,
            ])
        );

        $path = '/users';
        if (!empty($queryString)) {
            $path .= '?' . $queryString;
        }

        parent::__construct(
            method: RequestMethodInterface::METHOD_GET,
            path: $path,
            headers: [
                'Content-Type' => 'application/json'
            ]
        );
    }

    public function buildResponse(
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse,
    ): UserCollection {
        if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            throw new GetUserException(
                $httpRequest,
                $httpResponse,
                'Error fetching user'
            );
        }

        return UserCollection::fromScalarMaps(
            (array) \json_decode(
                (string) $httpResponse->getBody(),
                associative: true,
                flags: JSON_THROW_ON_ERROR
            )
        );
    }
}

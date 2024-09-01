<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\GetUserException;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\UserNotExistsException;
use Sokil\KeycloakAdminApiClient\Representation\User;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * {
        "id": "92ccf7ea-bc64-48e6-ba58-792db362beda",
        "createdTimestamp": 1661000371292,
        "username": "testuser-11",
        "enabled": true,
        "totp": false,
        "emailVerified": false,
        "firstName": "John",
        "lastName": "Dow",
        "email": "testuser-11@example.com",
        "attributes": {
        "TENANT_ID": [
              "42"
            ]
        },
        "disableableCredentialTypes": [],
        "requiredActions": [
            "VERIFY_EMAIL"
        ],
        "notBefore": 0,
        "access": {
            "manageGroupMembership": true,
            "view": true,
            "mapRoles": true,
            "impersonate": false,
            "manage": true
        }
    }
 */
class GetUserRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        Uuid $userId
    ) {
        parent::__construct(
            method: RequestMethodInterface::METHOD_GET,
            path: '/users/' . $userId,
            headers: [
                'Content-Type' => 'application/json'
            ]
        );
    }

    public function buildResponse(
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse,
    ): ?object {
        if ($httpResponse->getStatusCode() === StatusCodeInterface::STATUS_NOT_FOUND) {
            throw new UserNotExistsException(
                $httpRequest,
                $httpResponse,
                'User not exists'
            );
        } if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            throw new GetUserException(
                $httpRequest,
                $httpResponse,
                'Error fetching user'
            );
        }

        return User::fromScalarMap(
            (array) \json_decode(
                (string) $httpResponse->getBody(),
                associative: true,
                flags: JSON_THROW_ON_ERROR
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\GetUserException;
use Sokil\KeycloakAdminApiClient\Representation\RoleCollection;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GetRolesRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        bool $briefRepresentation = true
    ) {
        parent::__construct(
            method: RequestMethodInterface::METHOD_GET,
            path: '/roles?briefRepresentation=' . ($briefRepresentation ? 'true' : 'false'),
            headers: [
                'Content-Type' => 'application/json'
            ]
        );
    }

    public function buildResponse(RequestInterface $httpRequest, ResponseInterface $httpResponse,): ?object
    {
        if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            throw new GetUserException(
                $httpRequest,
                $httpResponse,
                'Error fetching realm roles'
            );
        }

        return RoleCollection::fromScalarMaps(
            (array) \json_decode(
                (string) $httpResponse->getBody(),
                associative: true,
                flags: JSON_THROW_ON_ERROR
            )
        );
    }
}

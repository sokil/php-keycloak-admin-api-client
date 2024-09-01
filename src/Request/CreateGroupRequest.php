<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\CreateGroupException;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\GroupAlreadyExistsException;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CreateGroupRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        string $name
    ) {
        parent::__construct(
            method: 'POST',
            path: '/groups',
            postBody: [
                'name' => $name,
            ],
            headers: [
                'Content-Type' => 'application/json'
            ]
        );
    }

    public function buildResponse(
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse,
    ): ?object {
        if ($httpResponse->getStatusCode() === StatusCodeInterface::STATUS_CONFLICT) {
            throw new GroupAlreadyExistsException(
                $httpRequest,
                $httpResponse,
                'Group already exists'
            );
        }
        if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_CREATED) {
            throw new CreateGroupException(
                $httpRequest,
                $httpResponse,
                'Error creating group'
            );
        }

        return new Url($httpResponse->getHeaderLine('Location'));
    }
}

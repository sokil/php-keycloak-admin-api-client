<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\ChangeUserPasswordException;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddUserRealmRoleRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        Uuid $userId,
        Uuid $roleId,
        string $roleName,
    ) {
        parent::__construct(
            method: 'POST',
            path: sprintf('/users/%s/role-mappings/realm', $userId->toRfc4122()),
            postBody: [
                [
                    'id' => $roleId->toRfc4122(),
                    'name' => $roleName,
                ]
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
        if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_NO_CONTENT) {
            throw new ChangeUserPasswordException(
                $httpRequest,
                $httpResponse,
                'Error adding realm role to user'
            );
        }

        return null;
    }
}

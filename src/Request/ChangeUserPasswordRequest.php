<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Representation\Credential;
use Sokil\KeycloakAdminApiClient\ValueObject\CredentialType;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\PlainPassword;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\ChangeUserPasswordException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ChangeUserPasswordRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        Uuid $userId,
        PlainPassword $password
    ) {
        $credential = new Credential(
            CredentialType::Password,
            $password,
            false
        );

        parent::__construct(
            method: 'PUT',
            path: sprintf('/users/%s/reset-password', $userId->toRfc4122()),
            postBody: $credential->toScalarMap(),
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
                'Error changing user password'
            );
        }

        return null;
    }
}

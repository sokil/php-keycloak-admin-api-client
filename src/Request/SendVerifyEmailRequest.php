<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\SendVerificationEmailError;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Sokil\KeycloakAdminApiClient\ValueObject\Uuid;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SendVerifyEmailRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    public function __construct(
        Uuid $userId,
        ?string $clientId = null,
        ?Url $redirectUri = null
    ) {
        parent::__construct(
            method: 'PUT',
            path: sprintf('/users/%s/send-verify-email', $userId->toRfc4122()),
            queryParams: array_filter([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri?->value,
            ]),
            postBody: [],
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
            throw new SendVerificationEmailError(
                $httpRequest,
                $httpResponse,
                'Error sending verification email'
            );
        }

        return null;
    }
}

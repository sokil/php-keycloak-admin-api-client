<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Request;

use Fig\Http\Message\StatusCodeInterface;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\CreateUserException;
use Sokil\KeycloakAdminApiClient\Exception\ServerError\UserAlreadyExistsException;
use Sokil\KeycloakAdminApiClient\Representation\CredentialCollection;
use Sokil\KeycloakAdminApiClient\ValueObject\UserAfterRegistrationAction;
use Sokil\RestApiClient\Auth\OAuth2\OAuth2AuthorizationAwareRequestInterface;
use Sokil\RestApiClient\Request\AbstractApiRequest;
use Sokil\KeycloakAdminApiClient\ValueObject\Email;
use Sokil\KeycloakAdminApiClient\ValueObject\Url;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CreateUserRequest extends AbstractApiRequest implements OAuth2AuthorizationAwareRequestInterface
{
    /**
     * @param string[] $groups
     * @param array<string, mixed> $attributes
     * @param string[] $realmRoles
     * @param UserAfterRegistrationAction[] $requiredActions
     */
    public function __construct(
        private readonly string $username,
        private readonly Email $email,
        private readonly bool $enabled,
        private readonly bool $emailVerified,
        private readonly ?CredentialCollection $credentials,
        private readonly ?string $firstName,
        private readonly ?string $lastName,
        private readonly array $groups,
        private readonly array $attributes,
        private readonly array $realmRoles,
        private readonly array $requiredActions,
    ) {
        if (empty($this->username)) {
            throw new \InvalidArgumentException('Username not defined');
        }

        $postBody = [
            "username" => $this->username,
            "email" => $this->email->getValue(),
            "enabled" => $this->enabled,
            "emailVerified" => $this->emailVerified,
        ];

        if ($this->credentials && count($this->credentials) > 0) {
            $postBody['credentials'] = $this->credentials->toScalarMaps();
        }

        if (null !== $this->firstName) {
            $postBody['firstName'] = $this->firstName;
        }

        if (null !== $this->lastName) {
            $postBody['lastName'] = $this->lastName;
        }

        if (count($this->groups) > 0) {
            foreach ($this->groups as $group) {
                if (!is_string($group)) {
                    throw new \InvalidArgumentException('Group name must be string');
                }
            }

            $postBody['groups'] = $this->groups;
        }

        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attributeValue) {
                if (!is_string($attributeValue)) {
                    throw new \InvalidArgumentException('Attribute value must be string');
                }
            }

            $postBody['attributes'] = $this->attributes;
        }

        if (count($this->realmRoles) > 0) {
            foreach ($this->realmRoles as $realmRole) {
                if (!is_string($realmRole)) {
                    throw new \InvalidArgumentException('Realm role name must be string');
                }
            }

            $postBody['realmRoles'] = $this->realmRoles;
        }

        if (count($this->requiredActions) > 0) {
            foreach ($this->requiredActions as $requiredAction) {
                if (!$requiredAction instanceof UserAfterRegistrationAction) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Required action must be instance of %s',
                            UserAfterRegistrationAction::class
                        )
                    );
                }
            }

            $postBody['requiredActions'] = array_map(
                function (UserAfterRegistrationAction $action) {
                    return $action->value;
                },
                $this->requiredActions
            );
        }

        parent::__construct(
            method: 'POST',
            path: '/users',
            postBody: $postBody,
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
            throw new UserAlreadyExistsException(
                $httpRequest,
                $httpResponse,
                'User already exists'
            );
        } if ($httpResponse->getStatusCode() !== StatusCodeInterface::STATUS_CREATED) {
            throw new CreateUserException(
                $httpRequest,
                $httpResponse,
                'Error creating user'
            );
        }

        return new Url($httpResponse->getHeaderLine('Location'));
    }
}

<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\Exception\ServerError;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractServerError extends \RuntimeException
{
    public function __construct(
        public readonly RequestInterface $request,
        public readonly ResponseInterface $response,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

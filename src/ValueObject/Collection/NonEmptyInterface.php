<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject\Collection;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\CollectionCanNotBeEmptyException;

/**
 * Any {@see AbstractImmutableCollection} implementing this interface will check the number of provided items. If
 * the list is empty {@see CollectionCanNotBeEmptyException} will be thrown
 */
interface NonEmptyInterface
{
}

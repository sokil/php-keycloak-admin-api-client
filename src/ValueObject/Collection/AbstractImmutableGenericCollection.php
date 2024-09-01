<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject\Collection;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\InvalidCollectionValueException;

/**
 * Strictly locked to type defined in {@see AbstractImmutableGenericCollection::getType}
 *
 * @psalm-template T
 * @extends AbstractImmutableCollection<T>
 */
abstract class AbstractImmutableGenericCollection extends AbstractImmutableCollection
{
    protected function validateValue(mixed $element): void
    {
        $typeClassName = $this->getType();

        if (!$element instanceof $typeClassName) {
            throw new InvalidCollectionValueException(
                sprintf('Each element of collection should be instance of %s', $typeClassName)
            );
        }
    }

    /**
     * @return string Class name of type
     * @psalm-return class-string<T>
     */
    abstract protected function getType(): string;
}

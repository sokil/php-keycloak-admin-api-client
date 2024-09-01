<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject\Collection;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\InvalidCollectionValueException;
use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\CollectionCanNotBeEmptyException;
use PHPUnit\Framework\TestCase;

class ImmutableCollectionTest extends TestCase
{
    public function testGetArrayCopy()
    {
        $scalarArray = [1, 3, 5, 7];

        $collection = new class ($scalarArray) extends AbstractImmutableCollection
        {
            protected function validateValue(mixed $element): void
            {
                if (!is_int($element)) {
                    throw new InvalidCollectionValueException('Must be int');
                }
            }
        };

        $this->assertEquals($scalarArray, $collection->getArrayCopy());
    }

    public function testMap()
    {
        $scalarArray = [1, 3, 5, 7];
        $expectedMappedArray = [11, 13, 15, 17];

        $collection = new class ($scalarArray) extends AbstractImmutableCollection
        {
            protected function validateValue(mixed $element): void
            {
                if (!is_int($element)) {
                    throw new InvalidCollectionValueException('Must be int');
                }
            }
        };

        $this->assertEquals(
            $expectedMappedArray,
            $collection->map(
                fn (int $value) => $value + 10
            )
        );
    }

    public function testInvalidEmptyListException(): void
    {
        $emptyArray = [];

        $this->expectException(CollectionCanNotBeEmptyException::class);

        new class ($emptyArray) extends AbstractImmutableCollection implements NonEmptyInterface
        {
            protected function validateValue(mixed $element): void
            {
            }
        };
    }
}

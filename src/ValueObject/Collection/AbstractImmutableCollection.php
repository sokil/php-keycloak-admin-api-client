<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject\Collection;

use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\InvalidCollectionValueException;
use Sokil\KeycloakAdminApiClient\ValueObject\Collection\Exception\CollectionCanNotBeEmptyException;

/**
 * May contain values of different types at the same time (scalars, objects of different classes)
 * as defined in {@see AbstractImmutableCollection::validateValue}
 *
 * @psalm-template T
 * @template-implements \Iterator<int, T>
 */
abstract class AbstractImmutableCollection implements \Iterator, \Countable
{
    /**
     * @var array<int, mixed>
     * @psalm-var array<int, T>
     */
    private array $items;

    private int $count;

    /**
     * Final to prevent constructor modifications in methods using "new static()"
     *
     * @param array $items
     * @psalm-param array<T> $items
     */
    final public function __construct(array $items)
    {
        $itemsCount = \count($items);
        if ($itemsCount === 0 && $this instanceof NonEmptyInterface) {
            throw new CollectionCanNotBeEmptyException();
        }

        array_map([$this, 'validateValue'], $items);

        $this->items = array_values($items);
        $this->count = $itemsCount;
    }

    /**
     * @psalm-return T
     */
    public function current(): mixed
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): mixed
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    /**
     * Copy collection storage content to array and return it
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return $this->items;
    }

    /**
     * @psalm-return T
     *
     * @throws \OutOfRangeException
     */
    public function get(int $index): mixed
    {
        if ($index > ($this->count + 1)) {
            throw new \OutOfRangeException('Invalid key');
        }

        return $this->items[$index];
    }

    /**
     * @return array<int, mixed>
     */
    public function map(callable $mapper): array
    {
        return array_map(
            $mapper,
            $this->items
        );
    }

    /**
     * @psalm-param callable(mixed, mixed):mixed $reducer
     * @param callable $reducer takes two arguments: carry and item
     * @param mixed $initialCarry initial value of carry
     *
     * @return mixed
     */
    public function reduce(callable $reducer, mixed $initialCarry = []): mixed
    {
        return array_reduce($this->items, $reducer, $initialCarry);
    }

    /**
     * @param AbstractImmutableCollection<T> $other
     * @return AbstractImmutableCollection<T>
     */
    public function merge(self $other): self
    {
        return new static(
            array_merge($this->items, $other->getArrayCopy())
        );
    }

    /**
     * @param callable $filter takes one argument: item
     *
     * @psalm-param callable(mixed, mixed=):scalar $filter
     *
     * @return AbstractImmutableCollection<T>
     */
    public function filter(callable $filter): AbstractImmutableCollection
    {
        return new static(array_filter($this->items, $filter));
    }

    /**
     * @param callable $sorter
     *
     * @psalm-param callable(mixed, mixed):int $sorter
     *
     * @return AbstractImmutableCollection<T>
     *@see http://php.net/manual/ru/function.usort.php
     *
     */
    public function sort(callable $sorter): AbstractImmutableCollection
    {
        $elements = $this->getArrayCopy();

        usort($elements, $sorter);

        return new static($elements);
    }

    /**
     * Return a collection with elements in reverse order
     *
     * @return AbstractImmutableCollection<T>
     */
    public function reverse(): AbstractImmutableCollection
    {
        $elements = array_reverse($this->getArrayCopy());

        return new static($elements);
    }

    /**
     * @return AbstractImmutableCollection<T>
     */
    public function slice(int $offset, int $length = null): self
    {
        return new static(
            array_slice($this->items, $offset, $length)
        );
    }

    /**
     * Determines whether the item is in the Collection
     *
     * @param mixed $needle
     * @return bool
     */
    public function contains($needle): bool
    {
        return in_array($needle, $this->items);
    }

    /**
     * By default method compares objects by "==" comparison operator.
     *
     * @link https://www.php.net/manual/en/language.oop5.object-comparison.php
     *
     * For comparison by custom conditions like entity identifier you need to override this method.
     *
     * @return AbstractImmutableCollection<T>
     */
    public function unique(): self
    {
        return new static(
            array_unique($this->items, SORT_REGULAR)
        );
    }

    /**
     * @return T
     */
    public function last(): mixed
    {
        $numberOfElements = $this->count();

        if ($numberOfElements) {
            return $this->get($numberOfElements - 1);
        }

        return null;
    }

    /**
     * Method which calls for validate each element in array when called collection constructor
     *
     * @throws InvalidCollectionValueException - for not valid elements
     */
    abstract protected function validateValue(mixed $element): void;
}

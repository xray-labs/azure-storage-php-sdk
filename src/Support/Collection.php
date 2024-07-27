<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Support;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @template TKey of array-key
 * @template TValue of object
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
class Collection implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @param array<TKey, TValue> $items */
    public function __construct(protected array $items = [])
    {
        //
    }

    /** @return array<TKey, TValue> */
    public function all(): array
    {
        return $this->items;
    }

    /** @return TValue|null */
    public function first(): ?object
    {
        return $this->get(0);
    }

    /** @return TValue|null */
    public function last(): ?object
    {
        return $this->get(count($this->items) - 1);
    }

    /**
     * @param TKey $key
     * @return TValue|null
     */
    public function get(int|string $key): ?object
    {
        return $this->items[$key] ?? null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    /** @return Traversable<TKey, TValue> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /** @return array<TKey, TValue> */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param TKey $offset
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): ?object
    {
        return $this->get($offset);
    }

    /**
     * @param TKey $offset
     * @param TValue $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /** @param TKey $offset */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}

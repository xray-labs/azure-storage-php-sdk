<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Support;

use ArrayIterator;
use JsonSerializable;
use Traversable;

class Collection extends ArrayIterator implements JsonSerializable
{
    public function __construct(protected array $items = [])
    {
        //
    }

    public function all(): array
    {
        return $this->items;
    }

    public function first(): ?object
    {
        return $this->get(0);
    }

    public function last(): ?object
    {
        return $this->get(count($this->items) - 1);
    }

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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ?object
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}

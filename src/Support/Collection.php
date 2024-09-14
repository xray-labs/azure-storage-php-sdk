<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Support;

use ArgumentCountError;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Xray\AzureStoragePhpSdk\Contracts\Arrayable;

/**
 * @template TKey
 * @template TValue
 *
 * @implements Arrayable<array<TKey, TValue>>>
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
class Collection implements Arrayable, IteratorAggregate, ArrayAccess, JsonSerializable
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

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TFirstDefault|(\Closure(): TFirstDefault) $default
     * @return TValue|TFirstDefault|null
     */
    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        /** @var TFirstDefault $default */
        $default = is_callable($default)
            ? call_user_func($default)
            : $default;

        if (is_null($callback)) {
            if ($this->isEmpty()) {
                return $default;
            }

            foreach ($this as $item) {
                return $item;
            }

            return $default; // @codeCoverageIgnore
        }

        foreach ($this as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item from the collection.
     *
     * @template TLastDefault
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TLastDefault|(\Closure(): TLastDefault)  $default
     * @return TValue|TLastDefault|null
     */
    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        /** @var TLastDefault $default */
        $default = is_callable($default)
            ? call_user_func($default)
            : $default;

        if (is_null($callback)) {
            if ($this->isEmpty()) {
                return $default;
            }

            foreach (array_reverse($this->items, true) as $item) {
                return $item;
            }

            return $default; // @codeCoverageIgnore
        }

        foreach (array_reverse($this->items, true) as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get an item from the collection by key.
     *
     * @template TGetDefault
     *
     * @param  (int&TKey)|(string&TKey)  $key
     * @param  TGetDefault|(\Closure(): TGetDefault)  $default
     * @return TValue|TGetDefault
     */
    public function get(string|int $key, mixed $default = null)
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return is_callable($default)
            ? call_user_func($default)
            : $default;
    }

    /**
     * Get the keys of the collection items.
     *
     * @return self<int, TKey>
     */
    public function keys(): static // @phpstan-ignore-line
    {
        // @phpstan-ignore-next-line
        return new static(array_keys($this->items));
    }

    /**
     * Count the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
    * Determine if the collection is empty or not.
    *
    * @phpstan-assert-if-true null $this->first()
    *
    * @phpstan-assert-if-false TValue $this->first()
    */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is not empty.
     *
     * @phpstan-assert-if-true TValue $this->first()
     *
     * @phpstan-assert-if-false null $this->first()
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Push one or more items onto the end of the collection.
     *
     * @param  TValue  ...$values
     */
    public function push(mixed ...$values): static
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  iterable<TKey, TValue>  $items
     */
    public function merge(iterable $items): static
    {
        // @phpstan-ignore-next-line
        return new static(array_merge($this->items, $items));
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param  iterable<(int&TKey)|(string&TKey), TValue>  $source
     * @return static<TKey, TValue>
     */
    public function concat(iterable $source): static // @phpstan-ignore-line
    {
        // @phpstan-ignore-next-line
        $result = new static($this->items);

        foreach ($source as $item) {
            $result->push($item);
        }

        return $result;
    }

    /**
      * Put an item in the collection by key.
      *
      * @param  (int&TKey)|(string&TKey)  $key
      * @param  TValue  $value
      */
    public function put(string|int $key, mixed $value): static
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param (iterable<array-key, TKey>)|(int&TKey)|(string&TKey) $keys
     */
    public function forget(iterable|string|int $keys): static
    {
        $keys = !is_iterable($keys) ? func_get_args() : $keys;

        /** @var TKey $key */
        foreach ($keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param (int&TKey)|(string&TKey) $key
     */
    public function pull(string|int $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->forget($key);

        return $value;
    }

    /**
      * Run a map over each of the items.
      *
      * @template TMapValue
      *
      * @param  callable(TValue, TKey): TMapValue  $callback
      * @return static<TKey, TMapValue>
      */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);

        try {
            $items = array_map($callback, $this->items, $keys);

            // @codeCoverageIgnoreStart
        } catch (ArgumentCountError) {
            $items = array_map($callback, $this->items); // @phpstan-ignore-line
        }
        // @codeCoverageIgnoreEnd

        // @phpstan-ignore-next-line
        return new static(array_combine($keys, $items));
    }

    /**
     * Execute a callback over each item.
     *
     * @param  callable(TValue, TKey): mixed  $callback
     * @return $this
     */
    public function each(callable $callback): static
    {
        foreach ($this as $key => $item) {
            if ($callback($item, $key) === false) {
                break; // @codeCoverageIgnore
            }
        }

        return $this;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null $callback
     * @param  int $mode - Available options are: 0: ARRAY_FILTER_USE_VALUE, 1: ARRAY_FILTER_USE_BOTH or 2: ARRAY_FILTER_USE_KEY
     * @return static
     */
    public function filter(?callable $callback = null, int $mode = ARRAY_FILTER_USE_BOTH)
    {
        if ($callback) {
            // @phpstan-ignore-next-line
            return new static(array_filter($this->items, $callback, $mode));
        }

        // @phpstan-ignore-next-line
        return new static(array_filter($this->items));
    }

    /**
     * Convert the collection to its array representation.
     *
     * @return array<TValue|array<mixed>>
     */
    public function toArray(): array
    {
        $items = $this->items;

        foreach ($items as $key => $item) {
            $items[$key] = match (true) {
                $item instanceof Arrayable        => $item->toArray(),
                $item instanceof JsonSerializable => $item->jsonSerialize(),
                default                           => $item,
            };
        }

        return $items;
    }

    /** @return ArrayIterator<(int&TKey)|(string&TKey), TValue> */
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
     * @param (int&TKey)|(string&TKey) $offset
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set an item in the collection by key.
     *
     * @param (int&TKey)|(string&TKey) $offset
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

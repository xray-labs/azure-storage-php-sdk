<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

/**
 * @template TKey of key-of-array
 * @template TValue of value-of-array
 */
interface Arrayable
{
    /** @return array<TKey, TValue> */
    public function toArray(): array;
}

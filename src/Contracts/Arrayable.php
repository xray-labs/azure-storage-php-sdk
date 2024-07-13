<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

/**
 * @template TValue of array
 */
interface Arrayable
{
    /** @return TValue */
    public function toArray(): array;
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Concerns;

use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

trait ValidateContainerName
{
    /** @throws InvalidArgumentException */
    protected function validateContainerName(string $name): void
    {
        $replaced = preg_replace('/[^a-z0-9-]/', '', $name);

        if ($replaced !== $name) {
            throw InvalidArgumentException::create("Invalid container name: {$name}");
        }
    }
}
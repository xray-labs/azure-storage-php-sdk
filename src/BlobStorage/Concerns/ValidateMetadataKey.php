<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Concerns;

use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

trait ValidateMetadataKey
{
    /** @throws InvalidArgumentException */
    protected function validateMetadataKey(string $key): void
    {
        $message = "Invalid metadata key: {$key}.";

        if (is_numeric($key[0])) {
            throw InvalidArgumentException::create("{$message} Metadata keys cannot start with a number.");
        }

        $name = preg_replace('/[^a-z0-9_]/i', '', $key);

        if ($key !== $name) {
            throw InvalidArgumentException::create("{$message} Only alphanumeric characters and underscores are allowed.");
        }
    }
}

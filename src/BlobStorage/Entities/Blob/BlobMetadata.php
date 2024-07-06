<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

final readonly class BlobMetadata
{
    /** @param array<string, scalar|null> $metadata */
    public function __construct(public array $metadata)
    {
        //
    }

    public function get(string $name): mixed
    {
        $this->validateMetadataKey(str_replace(Resource::METADATA_PREFIX, '', $name));

        if (!str_contains($name, Resource::METADATA_PREFIX)) {
            $name = Resource::METADATA_PREFIX . $name;
        }

        return $this->metadata[$name] ?? null;
    }

    public function has(string $name): bool
    {
        $this->validateMetadataKey(str_replace(Resource::METADATA_PREFIX, '', $name));

        if (!str_contains($name, Resource::METADATA_PREFIX)) {
            $name = Resource::METADATA_PREFIX . $name;
        }

        return isset($this->metadata[$name]);
    }

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

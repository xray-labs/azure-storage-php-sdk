<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\ValidateMetadataKey;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;

/**
 * @phpstan-type BlobMetadataHeaders array{Content-Length?: string, Last-Modified?: string, ETag?: string, Vary?: string, Server?: string, x-ms-request-id?: string, x-ms-version?: string, Date?: string}
 */
final readonly class BlobMetadata
{
    use ValidateMetadataKey;

    public ?int $contentLength;

    public ?DateTimeImmutable $lastModified;

    public ?string $eTag;

    public ?string $vary;

    public ?string $server;

    public ?string $xMsRequestId;

    public ?string $xMsVersion;

    public ?DateTimeImmutable $date;

    /**
     * @param array<string, scalar|null> $metadata
     * @param BlobMetadataHeaders $options
    */
    public function __construct(public array $metadata, array $options = [])
    {
        $this->contentLength = isset($options['Content-Length']) ? (int) $options['Content-Length'] : null;
        $this->lastModified  = isset($options['Last-Modified']) ? new DateTimeImmutable($options['Last-Modified']) : null;
        $this->eTag          = $options['ETag'] ?? null;
        $this->vary          = $options['Vary'] ?? null;
        $this->server        = $options['Server'] ?? null;
        $this->xMsRequestId  = $options['x-ms-request-id'] ?? null;
        $this->xMsVersion    = $options['x-ms-version'] ?? null;
        $this->date          = isset($options['Date']) ? new DateTimeImmutable($options['Date']) : null;
    }

    public function get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        $this->validateMetadataKey(str_replace(Resource::METADATA_PREFIX, '', $name));

        if (!str_contains($name, Resource::METADATA_PREFIX)) {
            $name = Resource::METADATA_PREFIX . $name;
        }

        return $this->metadata[$name] ?? null;
    }

    public function has(string $name): bool
    {
        if (property_exists($this, $name)) {
            return $this->{$name} !== null;
        }

        $this->validateMetadataKey(str_replace(Resource::METADATA_PREFIX, '', $name));

        if (!str_contains($name, Resource::METADATA_PREFIX)) {
            $name = Resource::METADATA_PREFIX . $name;
        }

        return isset($this->metadata[$name]);
    }

    /** @return array<string, scalar> */
    public function getMetadataToSave(): array
    {
        $metadata = [];

        foreach ($this->metadata as $name => $value) {
            $this->validateMetadataKey(str_replace(Resource::METADATA_PREFIX, '', $name));

            $key = !str_starts_with($name, Resource::METADATA_PREFIX)
                ? Resource::METADATA_PREFIX . $name
                : $name;

            $metadata[$key] = $value;
        }

        return array_filter($metadata, fn (mixed $value) => $value !== null);
    }
}

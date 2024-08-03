<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTime;
use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\ExpirationOption;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\{BlobLeaseManager, BlobManager, BlobTagManager};
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Concerns\HasManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/**
 * @phpstan-import-type PropertiesType from Properties
 *
 * @phpstan-type BlobType array{Name?: string, Snapshot?: string, Version?: string, IsCurrentVersion?: bool, Deleted?: bool, Properties?: PropertiesType}
 */
final class Blob
{
    /** @use HasManager<BlobManager> */
    use HasManager;

    public readonly string $name;

    public readonly ?DateTimeImmutable $snapshot;

    public readonly ?string $snapshotOriginalRaw;

    public readonly string $versionId;

    public readonly bool $isCurrentVersion;

    public readonly bool $deleted;

    public readonly Properties $properties;

    /** @param BlobType $blob */
    public function __construct(array $blob)
    {
        /** @var string $name */
        $name = ($blob['Name'] ?? '');

        if (empty($name)) {
            throw RequiredFieldException::missingField('Name'); // @codeCoverageIgnore
        }

        $this->name                = $name;
        $this->snapshot            = isset($blob['Snapshot']) ? new DateTimeImmutable($blob['Snapshot']) : null;
        $this->snapshotOriginalRaw = $blob['Snapshot'] ?? null;
        $this->versionId           = $blob['Version'] ?? '';
        $this->isCurrentVersion    = to_boolean($blob['IsCurrentVersion'] ?? true);

        $this->properties = new Properties($blob['Properties'] ?? []);

        $this->deleted = to_boolean($blob['Deleted'] ?? false);
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): File
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->get($this->name, $options);
    }

    /** @param array<string, scalar> $options */
    public function getProperties(array $options = []): BlobProperty
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->properties($this->name)->get($options);
    }

    /**
     * @param boolean $force If true, Delete the base blob and all of its snapshots.
     */
    public function delete(bool $force = false): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->delete($this->name, $this->snapshotOriginalRaw, $force);
    }

    /** @param array<string, scalar> $options */
    public function copy(string $destination, array $options = []): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->copy($this->name, $destination, $options, $this->snapshotOriginalRaw);
    }

    public function restore(): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->restore($this->name);
    }

    public function createSnapshot(): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->createSnapshot($this->name);
    }

    public function tags(): BlobTagManager
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->tags($this->name);
    }

    public function lease(): BlobLeaseManager
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->lease($this->name);
    }

    /** @param array<string, scalar> $options */
    public function setExpiry(ExpirationOption $option, null|int|DateTime $expiry, array $options = []): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->setExpiry($this->name, $option, $expiry, $options);
    }
}

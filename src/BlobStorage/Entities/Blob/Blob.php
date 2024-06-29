<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\Concerns\HasManager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

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

    public readonly DateTimeImmutable $snapshot;

    public readonly DateTimeImmutable $versionId;

    public readonly bool $isCurrentVersion;

    public readonly bool $deleted;

    public readonly Properties $properties;

    /** @param BlobType $blob */
    public function __construct(array $blob)
    {
        /** @var string $name */
        $name = ($blob['Name'] ?? '');

        if (empty($name)) {
            throw RequiredFieldException::missingField('Name');
        }

        $this->name             = $name;
        $this->snapshot         = new DateTimeImmutable($blob['Snapshot'] ?? 'now');
        $this->versionId        = new DateTimeImmutable($blob['Version'] ?? 'now');
        $this->isCurrentVersion = to_boolean($blob['IsCurrentVersion'] ?? true);

        $this->properties = new Properties($blob['Properties'] ?? []);

        $this->deleted = to_boolean($blob['Deleted'] ?? false);
    }

    public function get()
    {
        return $this->manager->get($this->name);
    }
}

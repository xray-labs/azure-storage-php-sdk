<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

final readonly class Properties
{
    public DateTimeImmutable $lastModified;

    public string $etag;

    public string $leaseStatus;

    public string $leaseState;

    public string $defaultEncryptionScope;

    public bool $denyEncryptionScopeOverride;

    public bool $hasImmutabilityPolicy;

    public bool $hasLegalHold;

    public bool $immutableStorageWithVersioningEnabled;

    public function __construct(array $property)
    {
        $this->lastModified                          = new DateTimeImmutable($property['Last-Modified'] ?? 'now');
        $this->etag                                  = $property['Etag'] ?? '';
        $this->leaseStatus                           = $property['LeaseStatus'] ?? '';
        $this->leaseState                            = $property['LeaseState'] ?? '';
        $this->defaultEncryptionScope                = $property['DefaultEncryptionScope'] ?? '';
        $this->denyEncryptionScopeOverride           = boolval($property['DenyEncryptionScopeOverride'] ?? false);
        $this->hasImmutabilityPolicy                 = boolval($property['HasImmutabilityPolicy'] ?? false);
        $this->hasLegalHold                          = boolval($property['HasLegalHold'] ?? false);
        $this->immutableStorageWithVersioningEnabled = boolval($property['ImmutableStorageWithVersioningEnabled'] ?? false);
    }
}

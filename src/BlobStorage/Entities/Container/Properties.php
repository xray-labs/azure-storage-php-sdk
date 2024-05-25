<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

readonly class Properties
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
        $this->denyEncryptionScopeOverride           = $property['DenyEncryptionScopeOverride'] ?? false;
        $this->hasImmutabilityPolicy                 = $property['HasImmutabilityPolicy'] ?? false;
        $this->hasLegalHold                          = $property['HasLegalHold'] ?? false;
        $this->immutableStorageWithVersioningEnabled = $property['ImmutableStorageWithVersioningEnabled'] ?? false;
    }
}

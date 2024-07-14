<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

/**
 * @phpstan-type PropertiesType array{Last-Modified?: string, Etag?: string, LeaseStatus?: string, LeaseState?: string, DefaultEncryptionScope?: string, DenyEncryptionScopeOverride?: bool, HasImmutabilityPolicy?: bool, HasLegalHold?: bool, ImmutableStorageWithVersioningEnabled?: bool, DeletedTime?: string, RemainingRetentionDays?: int}
 */
final readonly class Properties
{
    public DateTimeImmutable $lastModified;

    public string $eTag;

    public string $leaseStatus;

    public string $leaseState;

    public string $defaultEncryptionScope;

    public bool $denyEncryptionScopeOverride;

    public bool $hasImmutabilityPolicy;

    public bool $hasLegalHold;

    public bool $immutableStorageWithVersioningEnabled;

    public ?DateTimeImmutable $deletedTime;

    public ?int $remainingRetentionDays;

    /** @param PropertiesType $property */
    public function __construct(array $property)
    {
        $this->lastModified                          = new DateTimeImmutable($property['Last-Modified'] ?? 'now');
        $this->eTag                                  = $property['Etag'] ?? '';
        $this->leaseStatus                           = $property['LeaseStatus'] ?? '';
        $this->leaseState                            = $property['LeaseState'] ?? '';
        $this->defaultEncryptionScope                = $property['DefaultEncryptionScope'] ?? '';
        $this->denyEncryptionScopeOverride           = to_boolean($property['DenyEncryptionScopeOverride'] ?? false);
        $this->hasImmutabilityPolicy                 = to_boolean($property['HasImmutabilityPolicy'] ?? false);
        $this->hasLegalHold                          = to_boolean($property['HasLegalHold'] ?? false);
        $this->immutableStorageWithVersioningEnabled = to_boolean($property['ImmutableStorageWithVersioningEnabled'] ?? false);
        $this->deletedTime                           = isset($property['DeletedTime']) ? new DateTimeImmutable($property['DeletedTime']) : null;
        $this->remainingRetentionDays                = isset($property['RemainingRetentionDays']) ? (int) $property['RemainingRetentionDays'] : null;
    }
}

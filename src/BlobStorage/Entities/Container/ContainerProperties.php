<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

final readonly class ContainerProperties
{
    public DateTimeImmutable $lastModified;

    public string $eTag;

    public string $server;

    public string $xMsRequestId;

    public string $xMsVersion;

    public string $xMsLeaseStatus;

    public string $xMsLeaseState;

    public bool $xMsHasImmutabilityPolicy;

    public bool $xMsHasLegalHold;

    public bool $xMsImmutableStorageWithVersioningEnabled;

    public string $xMsDefaultEncryptionScopeOverride;

    public bool $xMsDenyEncryptionScopeOverride;

    public DateTimeImmutable $date;

    /** @param array<string> $containerProperty */
    public function __construct(array $containerProperty)
    {
        $this->lastModified                             = new DateTimeImmutable($containerProperty['Last-Modified'] ?? 'now');
        $this->eTag                                     = $containerProperty['ETag'] ?? '';
        $this->server                                   = $containerProperty['Server'] ?? '';
        $this->xMsRequestId                             = $containerProperty['x-ms-request-id'] ?? '';
        $this->xMsVersion                               = $containerProperty['x-ms-version'] ?? '';
        $this->xMsLeaseStatus                           = $containerProperty['x-ms-lease-status'] ?? '';
        $this->xMsLeaseState                            = $containerProperty['x-ms-lease-state'] ?? '';
        $this->xMsHasImmutabilityPolicy                 = to_boolean($containerProperty['x-ms-has-immutability-policy'] ?? '');
        $this->xMsHasLegalHold                          = to_boolean($containerProperty['x-ms-has-legal-hold'] ?? '');
        $this->xMsImmutableStorageWithVersioningEnabled = to_boolean($containerProperty['x-ms-immutable-storage-with-versioning-enabled'] ?? '');
        $this->xMsDefaultEncryptionScopeOverride        = $containerProperty['x-ms-default-encryption-scope'] ?? '';
        $this->xMsDenyEncryptionScopeOverride           = to_boolean($containerProperty['x-ms-deny-encryption-scope-override'] ?? '');
        $this->date                                     = new DateTimeImmutable($containerProperty['Date'] ?? 'now');
    }
}

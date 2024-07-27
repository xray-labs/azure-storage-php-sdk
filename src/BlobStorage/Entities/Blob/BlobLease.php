<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobLeaseManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Concerns\HasManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/**
 * @phpstan-type BlobLeaseType array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string}
 */
final class BlobLease
{
    /** @use HasManager<BlobLeaseManager> */
    use HasManager;

    public readonly DateTimeImmutable $lastModified;

    public readonly string $etag;

    public readonly string $server;

    public readonly string $requestId;

    public readonly string $version;

    public readonly ?string $leaseId;

    public readonly DateTimeImmutable $date;

    /** @param BlobLeaseType $blobLease */
    public function __construct(array $blobLease)
    {
        $this->lastModified = new DateTimeImmutable($blobLease['Last-Modified'] ?? 'now');
        $this->etag         = $blobLease['ETag'] ?? '';
        $this->server       = $blobLease['Server'] ?? '';
        $this->requestId    = $blobLease[Resource::REQUEST_ID] ?? '';
        $this->version      = $blobLease[Resource::AUTH_VERSION] ?? '';
        $this->date         = new DateTimeImmutable($blobLease['Date'] ?? 'now');

        $this->leaseId = $blobLease[Resource::LEASE_ID]
            ?? null;
    }

    public function renew(): self
    {
        $this->ensureLeaseIdIsset();

        return $this->manager->renew($this->leaseId);
    }

    public function change(string $toLeaseId): self
    {
        $this->ensureLeaseIdIsset();

        return $this->manager->change($this->leaseId, $toLeaseId);
    }

    public function release(string $leaseId): self
    {
        return $this->manager->release($leaseId);
    }

    public function break(?string $leaseId = null): self
    {
        return $this->manager->break($leaseId);
    }

    /** @phpstan-assert string $this->leaseId */
    protected function ensureLeaseIdIsset(): void
    {
        if (empty($this->leaseId)) {
            throw RequiredFieldException::missingField('leaseId');
        }
    }
}

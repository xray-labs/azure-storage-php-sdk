<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerLeaseManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Concerns\HasManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/**
 * @phpstan-type ContainerLeaseType array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string}
 */
final class ContainerLease
{
    /** @use HasManager<ContainerLeaseManager> */
    use HasManager;

    public readonly DateTimeImmutable $lastModified;

    public readonly string $eTag;

    public readonly string $server;

    public readonly string $requestId;

    public readonly string $version;

    public readonly ?string $leaseId;

    public readonly DateTimeImmutable $date;

    /** @param ContainerLeaseType $containerLease */
    public function __construct(array $containerLease)
    {
        $this->lastModified = new DateTimeImmutable($containerLease['Last-Modified'] ?? 'now');
        $this->eTag         = $containerLease['ETag'] ?? '';
        $this->server       = $containerLease['Server'] ?? '';
        $this->requestId    = $containerLease[Resource::REQUEST_ID] ?? '';
        $this->version      = $containerLease[Resource::AUTH_VERSION] ?? '';
        $this->date         = new DateTimeImmutable($containerLease['Date'] ?? 'now');

        $this->leaseId = $containerLease[Resource::LEASE_ID]
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
            throw RequiredFieldException::missingField('leaseId'); // @codeCoverageIgnore
        }
    }
}

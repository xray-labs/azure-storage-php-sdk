<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerLease;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

class ContainerLeaseManager implements Manager
{
    public function __construct(
        protected Request $request,
        protected string $container,
    ) {
        //
    }

    public function acquire(int $duration = -1, ?string $leaseId = null): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request(array_filter([
            Resource::LEASE_ACTION_KEY   => 'acquire',
            Resource::LEASE_DURATION_KEY => $duration,
            Resource::LEASE_ID_KEY       => $leaseId,
        ]))->getHeaders();

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function renew(string $leaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION_KEY => 'renew',
            Resource::LEASE_ID_KEY     => $leaseId,
        ])->getHeaders();

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function change(string $fromLeaseId, string $toLeaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION_KEY      => 'change',
            Resource::LEASE_ID_KEY          => $fromLeaseId,
            Resource::LEASE_PROPOSED_ID_KEY => $toLeaseId,
        ])->getHeaders();

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function release(string $leaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION_KEY => 'release',
            Resource::LEASE_ID_KEY     => $leaseId,
        ])->getHeaders();

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function break(?string $leaseId = null): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request(array_filter([
            Resource::LEASE_ACTION_KEY => 'break',
            Resource::LEASE_ID_KEY     => $leaseId,
        ]))->getHeaders();

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    /** @param array<string, scalar> $headers */
    protected function request(array $headers): Response
    {
        try {
            return $this->request
                ->withHeaders($headers)
                ->put("{$this->container}?comp=lease&restype=container");
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}

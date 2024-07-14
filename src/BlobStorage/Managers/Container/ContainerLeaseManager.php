<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Concerns\ValidateContainerName;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerLease;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

class ContainerLeaseManager implements Manager
{
    use ValidateContainerName;

    public function __construct(
        protected Request $request,
        protected string $container,
    ) {
        $this->validateContainerName($this->container);
    }

    public function acquire(int $duration = -1, ?string $leaseId = null): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request(array_filter([
            Resource::LEASE_ACTION   => 'acquire',
            Resource::LEASE_DURATION => $duration,
            Resource::LEASE_ID       => $leaseId,
        ]))->getHeaders();

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value);

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function renew(string $leaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION => 'renew',
            Resource::LEASE_ID     => $leaseId,
        ])->getHeaders();

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value);

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function change(string $fromLeaseId, string $toLeaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION      => 'change',
            Resource::LEASE_ID          => $fromLeaseId,
            Resource::LEASE_PROPOSED_ID => $toLeaseId,
        ])->getHeaders();

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value);

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function release(string $leaseId): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request([
            Resource::LEASE_ACTION => 'release',
            Resource::LEASE_ID     => $leaseId,
        ])->getHeaders();

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value);

        return (new ContainerLease($headers))
            ->setManager($this);
    }

    public function break(?string $leaseId = null): ContainerLease
    {
        /** @var array{'Last-Modified'?: string, ETag?: string, Server?: string, Date?: string, 'x-ms-request-id'?: string, 'x-ms-version'?: string, 'x-ms-lease-id'?: string} $headers */
        $headers = $this->request(array_filter([
            Resource::LEASE_ACTION => 'break',
            Resource::LEASE_ID     => $leaseId,
        ]))->getHeaders();

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value);

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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}

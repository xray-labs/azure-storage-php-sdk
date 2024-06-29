<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\Container;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\{ContainerProperties, Containers};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container\{
    ContainerAccessLevelManager,
    ContainerLeaseManager,
    ContainerMetadataManager,
};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Concerns\HasRequestShared as HasRequestSharedTrait;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\{Request};
use Sjpereira\AzureStoragePhpSdk\Contracts\{HasRequestShared, Manager};
use Sjpereira\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

/**
 * @phpstan-import-type ContainerType from Container
 */
readonly class ContainerManager implements Manager, HasRequestShared
{
    /** @use HasRequestSharedTrait<Request> */
    use HasRequestSharedTrait;

    public function __construct(protected Request $request)
    {
        //
    }

    public function accessLevel(): ContainerAccessLevelManager
    {
        return new ContainerAccessLevelManager($this->request);
    }

    public function metadata(): ContainerMetadataManager
    {
        return new ContainerMetadataManager($this->request);
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
     */
    public function getProperties(string $container, array $options = []): ContainerProperties
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$container}?restype=container")
                ->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        /** @var array<string> $response */
        return new ContainerProperties($response);
    }

    /** @param array<string, scalar> $options */
    public function list(array $options = [], bool $withDeleted = false): Containers
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=list' . ($withDeleted ? '&include=deleted' : ''))
                ->getBody();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var array{Containers?: array{Container: ContainerType|ContainerType[]}} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return new Containers($this, $parsed['Containers']['Container'] ?? []);
    }

    public function lease(string $name): ContainerLeaseManager
    {
        $this->validateContainerName($name);

        return new ContainerLeaseManager($this->request, $name);
    }

    public function create(string $name): bool
    {
        $this->validateContainerName($name);

        try {
            return $this->request
                ->put("{$name}?restype=container")
                ->isCreated();
        } catch (RequestExceptionInterface) {
            return false;
        }
    }

    public function delete(string $name): bool
    {
        $this->validateContainerName($name);

        try {
            return $this->request
                ->delete("{$name}?restype=container")
                ->isAccepted();
        } catch (RequestExceptionInterface) {
            return false;
        }
    }

    public function restore(string $name, string $version): bool
    {
        $this->validateContainerName($name);

        try {
            return $this->request
                ->withHeaders([
                    Resource::DELETE_CONTAINER_NAME_KEY    => $name,
                    Resource::DELETE_CONTAINER_VERSION_KEY => $version,
                ])
                ->put("{$name}?comp=undelete&restype=container")
                ->isCreated();
        } catch (RequestExceptionInterface) {
            return false;
        }
    }

    protected function validateContainerName(string $name): void
    {
        $replaced = preg_replace('/[^a-z0-9-]/', '', $name);

        if ($replaced !== $name) {
            throw InvalidArgumentException::create("Invalid container name: {$name}");
        }
    }
}

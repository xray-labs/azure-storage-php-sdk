<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\Containers;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container\{
    ContainerAccessLevelManager,
    ContainerMetadataManager,
    ContainerPropertyManager,
};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

readonly class ContainerManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function accessLevel(): ContainerAccessLevelManager
    {
        return new ContainerAccessLevelManager($this->request);
    }

    public function properties(): ContainerPropertyManager
    {
        return new ContainerPropertyManager($this->request);
    }

    public function metadata(): ContainerMetadataManager
    {
        return new ContainerMetadataManager($this->request);
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

        /**
         * @var ?array{
         *   Containers: array{
         *     Container: array<array<mixed>>
         *   }
         * }
        */
        $parsed = $this->request->config->parser->parse($response);

        return new Containers($this, $parsed['Containers']['Container'] ?? []);
    }

    public function create(string $name): bool
    {
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

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
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

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
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

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
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\{ContainerLevelAccess, ContainerMetadata, ContainerProperty, Containers};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

readonly class ContainerManager
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function levelAccess(string $name, array $options = []): ContainerLevelAccess
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$name}?comp=acl&restype=container")
                ->getBody()
                ->getContents();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        $parsed = $this->request->config->parser->parse($response);

        return new ContainerLevelAccess($parsed['SignedIdentifiers'] ?? []);
    }

    public function properties(string $name, array $options = []): ContainerProperty
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$name}?restype=container")
                ->getHeaders();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        return new ContainerProperty($response);
    }

    public function metadata(string $name, array $options = []): ContainerMetadata
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$name}?comp=metadata&restype=container")
                ->getHeaders();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        return new ContainerMetadata($response);
    }

    public function list(array $options = [], bool $withDeleted = false): Containers
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=list' . ($withDeleted ? '&include=deleted' : ''))
                ->getBody()
                ->getContents();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        $parsed = $this->request->config->parser->parse($response);

        return new Containers($this, $parsed['Containers']['Container'] ?? []);
    }

    public function create(string $name): bool
    {
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

        try {
            $response = $this->request->put("{$name}?restype=container");

            return $response->getStatusCode() === 201;
        } catch (RequestException $e) {
            return false;
        }
    }

    public function delete(string $name): bool
    {
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

        try {
            $response = $this->request->delete("{$name}?restype=container");

            return $response->getStatusCode() === 202;
        } catch (RequestException $e) {
            return false;
        }
    }

    public function restore(string $name, string $version): bool
    {
        // TODO: Validate if it's a valid url container (lower case, number and hyphen)

        try {
            $response = $this->request
                ->withHeaders([
                    Resource::DELETE_CONTAINER_NAME_KEY    => $name,
                    Resource::DELETE_CONTAINER_VERSION_KEY => $version,
                ])
                ->put("{$name}?comp=undelete&restype=container");

            return $response->getStatusCode() === 201;
        } catch (RequestException $e) {
            return false;
        }
    }
}

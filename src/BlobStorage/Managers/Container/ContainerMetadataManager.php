<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerMetadata;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

class ContainerMetadataManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
     */
    public function get(string $container, array $options = []): ContainerMetadata
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$container}?comp=metadata&restype=container")
                ->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        /** @var array<string> $response */
        return new ContainerMetadata($response);
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $parameters
     */
    public function save(string $container, array $parameters): bool
    {
        $headers = [];

        foreach ($parameters as $key => $value) {
            $name           = Resource::CONTAINER_META_PREFIX . str_camel_to_header($key);
            $headers[$name] = $value;
        }

        try {
            return $this->request
                ->withHeaders($headers)
                ->put("{$container}?restype=container&comp=metadata")
                ->isOk();
        } catch (RequestExceptionInterface) {
            return false;
        }
    }
}

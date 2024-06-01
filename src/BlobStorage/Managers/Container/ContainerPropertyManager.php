<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerProperty;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

class ContainerPropertyManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
     */
    public function list(string $container, array $options = []): ContainerProperty
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
        return new ContainerProperty($response);
    }
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Concerns\{ValidateContainerName, ValidateMetadataKey};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerMetadata;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\{RequestException};

readonly class ContainerMetadataManager implements Manager
{
    use ValidateMetadataKey;
    use ValidateContainerName;

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
        $this->validateContainerName($container);

        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$container}?comp=metadata&restype=container")
                ->getHeaders();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        array_walk($response, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value); // @phpstan-ignore-line

        /** @var array<string> $response */
        return new ContainerMetadata($response);
    }

    /**
     * @param string $container Name of the container
     * @param array<string, string> $parameters
     */
    public function save(string $container, array $parameters): bool
    {
        $this->validateContainerName($container);

        $headers = [];

        foreach ($parameters as $key => $value) {
            $this->validateMetadataKey($key);
            $headers[Resource::METADATA_PREFIX . $key] = urlencode($value);
        }

        try {
            return $this->request
                ->withHeaders($headers)
                ->put("{$container}?restype=container&comp=metadata")
                ->isOk();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}

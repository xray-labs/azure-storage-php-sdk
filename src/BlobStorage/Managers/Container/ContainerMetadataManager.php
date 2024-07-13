<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerMetadata;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

readonly class ContainerMetadataManager implements Manager
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
     * @param array<string, string> $parameters
     */
    public function save(string $container, array $parameters): bool
    {
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
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    protected function validateMetadataKey(string $key): void
    {
        $message = "Invalid metadata key: {$key}.";

        if (is_numeric($key[0])) {
            throw InvalidArgumentException::create("{$message} Metadata keys cannot start with a number.");
        }

        $name = preg_replace('/[^a-z0-9_]/i', '', $key);

        if ($key !== $name) {
            throw InvalidArgumentException::create("{$message} Only alphanumeric characters and underscores are allowed.");
        }
    }
}

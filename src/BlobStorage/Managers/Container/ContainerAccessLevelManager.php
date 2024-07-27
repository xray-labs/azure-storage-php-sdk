<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\ValidateContainerName;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel\{
    ContainerAccessLevel,
    ContainerAccessLevels,
};
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

readonly class ContainerAccessLevelManager implements Manager
{
    use ValidateContainerName;

    public function __construct(protected Request $request)
    {
        //
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
    */
    public function list(string $container, array $options = []): ContainerAccessLevels
    {
        $this->validateContainerName($container);

        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$container}?comp=acl&restype=container")
                ->getBody();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var array<array<array<mixed>>> */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return new ContainerAccessLevels($this, $parsed['SignedIdentifier'] ?? []);
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
    */
    public function save(string $container, ContainerAccessLevel $accessLevel, array $options = []): bool
    {
        $this->validateContainerName($container);

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([Resource::CONTENT_TYPE => 'application/xml'])
                ->put("{$container}?comp=acl&restype=container", $accessLevel->toXML())
                ->isOk();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface) {
            return false;
        }
        // @codeCoverageIgnoreEnd
    }
}

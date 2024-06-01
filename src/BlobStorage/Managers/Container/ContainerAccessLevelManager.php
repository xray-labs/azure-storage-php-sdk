<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container;

use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel\{ContainerAccessLevel, ContainerAccessLevels};
use Sjpereira\AzureStoragePhpSdk\Http\Request;

readonly class ContainerAccessLevelManager
{
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
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$container}?comp=acl&restype=container")
                ->getBody();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        /** @var array<array<array<mixed>>> */
        $parsed = $this->request->config->parser->parse($response);

        return new ContainerAccessLevels($this, $parsed['SignedIdentifier'] ?? []);
    }

    /**
     * @param string $container Name of the container
     * @param array<string, scalar> $options
    */
    public function save(string $container, ContainerAccessLevel $accessLevel, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->put("{$container}?comp=acl&restype=container", $accessLevel->toXML())
                ->isOk();
        } catch (RequestException $e) {
            return false;
        }
    }
}

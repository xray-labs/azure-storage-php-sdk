<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\Containers;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

readonly class ContainerManager
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function list(array $options = []): Containers
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=list')
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
}

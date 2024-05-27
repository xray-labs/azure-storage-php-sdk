<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities;

use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

final readonly class Account
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function information(array $options = []): AccountInformation
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=account')
                ->getHeaders();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        return new AccountInformation($response);
    }

    public function blobServiceProperties(array $options = [])
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=service')
                ->getBody()
                ->getContents();
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }

        $parsed = $this->request->config->parser->parse($response);

        return new BlobProperty($parsed ?? []);
    }

    public function setBlobStorageProperties(array $options = [])
    {
        // TODO: Implement setBlobStorageProperties() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/set-blob-service-properties?tabs=microsoft-entra-id
    }

    public function preflightBlobRequest(array $options = [])
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/preflight-blob-request
    }

    public function getBlobServiceStats(array $options = [])
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/get-blob-service-stats?tabs=microsoft-entra-id
    }
}

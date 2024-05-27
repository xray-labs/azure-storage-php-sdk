<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities;

use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\Containers;
use Sjpereira\AzureStoragePhpSdk\Http\{Request};
use Sjpereira\AzureStoragePhpSdk\Parsers\Contracts\Parser;

class Account
{
    public function __construct(protected Request $request, protected Parser $parser)
    {
        //
    }

    public function getAccountInformation(array $options = [])
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=account')
                ->getHeaders();

            array_walk($response, function (&$value) {
                $value = $value[0];
            });

            return new AccountInformation($response);
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }
    }

    public function listContainers(array $options = []): Containers
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=list')
                ->getBody()
                ->getContents();

            /** @var DOMDocument $parsed */
            $parsed = $this->parser->parse($response);

            return new Containers($parsed['Containers']['Container'] ?? []);
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }
    }

    public function getBlobServiceProperties(array $options = [])
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=service')
                ->getBody()
                ->getContents();

            /** @var DOMDocument $parsed */
            $parsed = $this->parser->parse($response);

            return new BlobProperty($parsed ?? []);
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }
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

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

final readonly class Account
{
    public function __construct(protected Request $request)
    {
        //
    }

    /** @param array<string, scalar> $options */
    public function information(array $options = []): AccountInformation
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=account')
                ->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        array_walk($response, fn (array &$value) => $value = current($value));

        /**
         * @var array{
         *  Server: ?string,
         *  x-ms-request-id: ?string,
         *  x-ms-version: ?string,
         *  x-ms-sku-name: ?string,
         *  x-ms-account-kind: ?string,
         *  x-ms-is-hns-enabled: ?bool,
         *  Date: ?string
         * } $response
         * */
        return new AccountInformation($response);
    }

    /** @param array<string, scalar> $options */
    public function blobServiceProperties(array $options = []): BlobProperty
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=service')
                ->getBody();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var ?array<mixed> $parsed */
        $parsed = $this->request->config->parser->parse($response);

        return new BlobProperty($parsed ?? []);
    }

    /** @param array<string, scalar> $options */
    public function setBlobStorageProperties(array $options = []): void
    {
        // TODO: Implement setBlobStorageProperties() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/set-blob-service-properties?tabs=microsoft-entra-id
    }

    /** @param array<string, scalar> $options */
    public function preflightBlobRequest(array $options = []): void
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/preflight-blob-request
    }

    /** @param array<string, scalar> $options */
    public function getBlobServiceStats(array $options = []): void
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/get-blob-service-stats?tabs=microsoft-entra-id
    }
}

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

    /**
     * Undocumented function
     *
     * @param array<string, scalar> $options
     * @return AccountInformation
     */
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

    /**
     * Undocumented function
     *
     * @param array<string, scalar> $options
     * @return BlobProperty
     */
    public function blobServiceProperties(array $options = []): BlobProperty
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
        /** @var ?array<mixed> $parsed */

        return new BlobProperty($parsed ?? []);
    }

    /**
     * Undocumented function
     *
     * @param array<string, scalar> $options
     * @return void
     */
    public function setBlobStorageProperties(array $options = [])
    {
        // TODO: Implement setBlobStorageProperties() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/set-blob-service-properties?tabs=microsoft-entra-id
    }

    /**
     * Undocumented function
     *
     * @param array<string, scalar> $options
     * @return void
     */
    public function preflightBlobRequest(array $options = [])
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/preflight-blob-request
    }

    /**
     * Undocumented function
     *
     * @param array<string, scalar> $options
     * @return void
     */
    public function getBlobServiceStats(array $options = []): void
    {
        // TODO: Implement preflightBlobRequest() method.
        // https://learn.microsoft.com/en-us/rest/api/storageservices/get-blob-service-stats?tabs=microsoft-entra-id
    }
}

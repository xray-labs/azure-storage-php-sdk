<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\AccountInformation;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account\StoragePropertyManager;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

readonly class AccountManager implements Manager
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

    public function storageProperties(): StoragePropertyManager
    {
        return new StoragePropertyManager($this->request);
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

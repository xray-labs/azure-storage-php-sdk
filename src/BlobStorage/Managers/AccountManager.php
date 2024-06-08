<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\{AccountInformation, GeoReplication};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account\{PreflightBlobRequestManager, StoragePropertyManager};
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

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

    public function preflightBlobRequest(): PreflightBlobRequestManager
    {
        return new PreflightBlobRequestManager($this->request);
    }

    /** @param array<string, scalar> $options */
    public function blobServiceStats(array $options = []): GeoReplication
    {
        try {
            $response = $this->request
                ->usingAccount(fn (string $account): string => "{$account}-secondary")
                ->withOptions($options)
                ->get('?comp=stats&restype=service')
                ->getBody();

            /** @var array{GeoReplication: array{Status: string, LastSyncTime: string}} $parsed */
            $parsed = $this->request->getConfig()->parser->parse($response);

            return new GeoReplication($parsed['GeoReplication']);
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}
//

<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\{
    AccountInformation,
    GeoReplication,
    KeyInfo,
    UserDelegationKey,
};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Account\{PreflightBlobRequestManager, StoragePropertyManager};
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

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
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd

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
        return azure_app(AccountInformation::class, ['accountInformation' => $response]);
    }

    public function storageProperties(): StoragePropertyManager
    {
        return azure_app(StoragePropertyManager::class);
    }

    public function preflightBlobRequest(): PreflightBlobRequestManager
    {
        return azure_app(PreflightBlobRequestManager::class);
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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var array{GeoReplication: array{Status: string, LastSyncTime: string}} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return azure_app(GeoReplication::class, ['geoReplication' => $parsed['GeoReplication']]);
    }

    /** @param array<string, scalar> $options */
    public function userDelegationKey(KeyInfo $keyInfo, array $options = []): UserDelegationKey
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->post('?comp=userdelegationkey&restype=service', $keyInfo->toXml())
                ->getBody();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var array{SignedOid: string, SignedTid: string, SignedStart: string, SignedExpiry: string, SignedService: string, SignedVersion: string, Value: string} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return azure_app(UserDelegationKey::class, ['userDelegationKey' => $parsed]);
    }
}

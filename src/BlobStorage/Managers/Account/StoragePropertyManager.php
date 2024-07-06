<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobStorageProperty\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobPropertyType from BlobProperty
 */
readonly class StoragePropertyManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): BlobProperty
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=properties&restype=service')
                ->getBody();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var ?BlobPropertyType $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return new BlobProperty($parsed ?? []);
    }

    /** @param array<string, scalar> $options */
    public function save(BlobProperty $blobProperty, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->put('?comp=properties&restype=service', $blobProperty->toXml())
                ->isAccepted();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}

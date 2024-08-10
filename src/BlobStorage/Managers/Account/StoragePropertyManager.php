<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Account;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\BlobProperty;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var ?BlobPropertyType $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return azure_app(BlobProperty::class, ['blobProperty' => $parsed ?? []]);
    }

    /** @param array<string, scalar> $options */
    public function save(BlobProperty $blobProperty, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([Resource::CONTENT_TYPE => 'application/xml'])
                ->put('?comp=properties&restype=service', $blobProperty->toXml())
                ->isAccepted();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobPropertyHeaders from BlobProperty
 */
class BlobPropertyManager implements Manager
{
    public function __construct(
        protected Request $request,
        protected string $containerName,
        protected string $blobName,
    ) {
        //
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): BlobProperty
    {
        try {
            /** @var BlobPropertyHeaders $headers */
            $headers = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$this->blobName}?resttype=blob")
                ->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        return new BlobProperty((array) $headers);
    }

    /** @param array<string, scalar> $options */
    public function save(BlobProperty $blobProperty, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders($blobProperty->getPropertiesToSave())
                ->put("{$this->containerName}/{$this->blobName}?comp=properties&resttype=blob")
                ->isOk();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}

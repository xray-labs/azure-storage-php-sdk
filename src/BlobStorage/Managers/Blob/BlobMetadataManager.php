<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobMetadata;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobMetadataHeaders from BlobMetadata
 */
class BlobMetadataManager implements Manager
{
    public function __construct(
        protected Request $request,
        protected string $containerName,
        protected string $blobName,
    ) {
        //
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): BlobMetadata
    {
        try {
            /** @var BlobMetadataHeaders $headers */
            $headers = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$this->blobName}?comp=metadata&resttype=blob")
                ->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var array<string, scalar> $metadata */
        $metadata = array_filter(
            (array)$headers,
            static fn (string $key) => str_starts_with($key, Resource::METADATA_PREFIX),
            ARRAY_FILTER_USE_KEY,
        );

        return new BlobMetadata($metadata, (array) $headers);
    }

    /** @param array<string, scalar> $options */
    public function save(BlobMetadata $blobMetadata, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders($blobMetadata->getMetadataToSave())
                ->put("{$this->containerName}/{$this->blobName}?comp=metadata&resttype=blob")
                ->isOk();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}

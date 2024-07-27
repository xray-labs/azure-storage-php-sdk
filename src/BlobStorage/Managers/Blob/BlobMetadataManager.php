<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobMetadata;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs, File};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\BlobType;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Queries\BlobTagQuery;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobType from Blob as BlobTypeStan
 * @phpstan-import-type FileType from File
 */
readonly class BlobManager implements Manager
{
    public function __construct(protected Request $request, protected string $containerName)
    {
        //
    }

    /** @param array<string, scalar> $options */
    public function list(array $options = [], bool $withDeleted = false): Blobs
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/?restype=container&comp=list")
                ->getBody();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var array{Blobs?: array{Blob: BlobTypeStan|BlobTypeStan[]}} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return new Blobs($this, $parsed['Blobs']['Blob'] ?? []);
    }

    /**
     * Find Blobs by Tags operation finds all blobs in the storage account whose tags match a search expression.
     * @param array<string, scalar> $options
     * @return BlobTagQuery<BlobManager, Blobs>
     */
    public function findByTag(array $options = []): BlobTagQuery
    {
        /** @var BlobTagQuery<BlobManager, Blobs> */
        return (new BlobTagQuery($this))
            ->whenBuild(function (string $query) use ($options): Blobs {
                try {
                    $response = $this->request
                        ->withOptions($options)
                        ->get("{$this->containerName}/?restype=container&comp=blobs&where={$query}")
                        ->getBody();
                } catch (RequestExceptionInterface $e) {
                    throw RequestException::createFromRequestException($e);
                }

                /** @var array{Blobs?: array{Blob: BlobTypeStan|BlobTypeStan[]}} $parsed */
                $parsed = $this->request->getConfig()->parser->parse($response);

                return new Blobs($this, $parsed['Blobs']['Blob'] ?? []);
            });

    }

    /** @param array<string, scalar> $options */
    public function get(string $blobName, array $options = []): File
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$blobName}?resttype=blob");

            $content = $response->getBody();

            /** @var FileType $headers */
            $headers = $response->getHeaders();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        return new File($blobName, $content, (array)$headers);
    }

    /** @param array<string, scalar> $options */
    public function putBlock(File $file, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::BLOB_TYPE         => BlobType::BLOCK->value,
                    Resource::BLOB_CONTENT_MD5  => $file->contentMD5,
                    Resource::BLOB_CONTENT_TYPE => $file->contentType,
                    Resource::CONTENT_MD5       => $file->contentMD5,
                    Resource::CONTENT_TYPE      => $file->contentType,
                    Resource::CONTENT_LENGTH    => $file->contentLength,
                ])
                ->put("{$this->containerName}/{$file->name}?resttype=blob", $file->content)
                ->isCreated();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    public function pages(): BlobPageManager
    {
        return (new BlobPageManager($this->request, $this->containerName))
            ->setManager($this);
    }

    public function properties(string $blobName): BlobPropertyManager
    {
        return new BlobPropertyManager($this->request, $this->containerName, $blobName);
    }

    public function metadata(string $blobName): BlobMetadataManager
    {
        return new BlobMetadataManager($this->request, $this->containerName, $blobName);
    }

    public function tags(string $blobName): BlobTagManager
    {
        return new BlobTagManager($this->request, $this->containerName, $blobName);
    }
}

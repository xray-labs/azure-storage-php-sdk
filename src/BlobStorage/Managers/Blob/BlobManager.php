<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs, File};
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobType from Blob
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

        /** @var array{Blobs?: array{Blob: BlobType|BlobType[]}} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($response);

        return new Blobs($this, $parsed['Blobs']['Blob'] ?? []);
    }

    /** @param array<string, scalar> $options */
    public function get(string $blobName, array $options = []): File
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$blobName}?resttype=blob");

            $body = $response->getBody();

            /** @var FileType $headers */
            $headers = $response->getHeaders();

            $headers['Name'] = $blobName;
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        return new File($body, (array)$headers);
    }

    /** @param array<string, scalar> $options */
    public function put(string $blobName, string $content, array $options = []): void
    {
        try {
            $this->request
                ->withOptions($options)
                ->withHeaders([
                    'x-ms-blob-type'        => 'BlockBlob',
                    'x-ms-blob-content-md5' => base64_encode(md5($content, binary: true)),
                ])
                ->put("{$this->containerName}/{$blobName}?resttype=blob", $content);
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    public function properties(string $blobName): BlobPropertyManager
    {
        return new BlobPropertyManager($this->request, $this->containerName, $blobName, );
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

<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use DateTime;
use DateTimeImmutable;
use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\{BlobIncludeOption, BlobType, ExpirationOption};
use Xray\AzureStoragePhpSdk\BlobStorage\Queries\BlobTagQuery;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

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

    /**
     * @param array<string, scalar> $options
     * @param string[] $includes
     */
    public function list(array $options = [], array $includes = []): Blobs
    {
        if (array_diff($includes, $availableOptions = BlobIncludeOption::toArray()) !== []) {
            throw InvalidArgumentException::create(sprintf("Invalid include option. \nValid options: %s", implode(', ', $availableOptions)));
        }

        $include = '';

        if (!empty($includes)) {
            $include = sprintf('&include=%s', implode(',', $includes));
        }

        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/?restype=container&comp=list{$include}")
                ->getBody();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        $headers = (array) $headers;
        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value); // @phpstan-ignore-line

        return new File($blobName, $content, $headers);
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

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function setExpiry(string $blobName, ExpirationOption $expirationOption, null|int|DateTime $expiryTime = null, array $options = []): bool
    {
        $this->validateExpirationTime($expirationOption, $expiryTime);

        $formattedExpirationTime = $expiryTime instanceof DateTime
            ? convert_to_RFC1123($expiryTime)
            : $expiryTime;

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders(array_filter([
                    Resource::EXPIRY_OPTION => $expirationOption->value,
                    Resource::EXPIRY_TIME   => $formattedExpirationTime,
                ]))
                ->put("{$this->containerName}/{$blobName}?resttype=blob&comp=expiry")
                ->isOk();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /**
     * @param boolean $force If true, Delete the base blob and all of its snapshots.
     */
    public function delete(string $blobName, null|DateTimeImmutable|string $snapshot = null, bool $force = false): bool
    {
        if ($snapshot instanceof DateTimeImmutable) {
            $snapshot = convert_to_RFC3339_micro($snapshot);
        }

        $snapshotHeader = $snapshot ? sprintf('?snapshot=%s', urlencode($snapshot)) : '';

        $deleteSnapshotHeader = $snapshot ? sprintf('&%s=only', Resource::DELETE_SNAPSHOTS) : '';

        if ($force) {
            $deleteSnapshotHeader = sprintf('&%s=include', Resource::DELETE_SNAPSHOTS);
        }

        try {
            return $this->request
                ->delete("{$this->containerName}/{$blobName}?resttype=blob{$snapshotHeader}{$deleteSnapshotHeader}")
                ->isAccepted();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    public function restore(string $blobName): bool
    {
        try {
            return $this->request
                ->put("{$this->containerName}/{$blobName}?comp=undelete&resttype=blob")
                ->isOk();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    public function createSnapshot(string $blobName): bool
    {
        try {
            return $this->request
                ->put("{$this->containerName}/{$blobName}?comp=snapshot&resttype=blob")
                ->isCreated();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /** @param array<string, scalar> $options */
    public function copy(string $sourceCopy, string $blobName, array $options = [], null|DateTimeImmutable|string $snapshot = null): bool
    {
        if ($snapshot instanceof DateTimeImmutable) {
            $snapshot = convert_to_RFC3339_micro($snapshot);
        }

        $snapshotHeader = $snapshot ? sprintf('?snapshot=%s', urlencode($snapshot)) : '';

        $sourceUri = $this->request->uri("{$this->containerName}/{$sourceCopy}{$snapshotHeader}");

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::COPY_SOURCE => $sourceUri,
                ])
                ->put("{$this->containerName}/{$blobName}?resttype=blob")
                ->isAccepted();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    public function lease(string $blobName): BlobLeaseManager
    {
        return new BlobLeaseManager($this->request, $this->containerName, $blobName);
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

    protected function validateExpirationTime(ExpirationOption $expirationOption, null|int|DateTime $expiryTime = null): void
    {
        match (true) {
            $expirationOption === ExpirationOption::NEVER_EXPIRE && $expiryTime !== null        => throw InvalidArgumentException::create('The expiration time must be null when the option is never expire.'),
            $expirationOption !== ExpirationOption::NEVER_EXPIRE && $expiryTime === null        => throw InvalidArgumentException::create('The expiration time must be informed when the option is not never expire.'),
            is_int($expiryTime) && $expirationOption === ExpirationOption::ABSOLUTE             => throw InvalidArgumentException::create('The expiration time must be an instance of DateTime.'),
            is_int($expiryTime) && $expiryTime < 0                                              => throw InvalidArgumentException::create('The expiration time must be a positive integer.'),
            $expiryTime instanceof DateTime && $expirationOption !== ExpirationOption::ABSOLUTE => throw InvalidArgumentException::create('The expiration time must be informed in milliseconds.'),
            default                                                                             => true,
        };
    }
}

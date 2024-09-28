<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\Authentication\SharedAccessSignature\UserDelegationSas;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\{AccessTokenPermission, BlobIncludeOption, BlobType, ExpirationOption};
use Xray\AzureStoragePhpSdk\BlobStorage\Queries\BlobTagQuery;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

use function Xray\AzureStoragePhpSdk\Support\{convert_to_RFC1123, convert_to_RFC3339_micro};

/**
 * @phpstan-import-type BlobType from Blob as BlobTypeStan
 * @phpstan-import-type FileType from File
 */
class BlobManager implements Manager
{
    public function __construct(
        protected readonly Request $request,
        protected readonly string $containerName,
    ) {
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

        return azure_app(Blobs::class, ['blobs' => $parsed['Blobs']['Blob'] ?? [], 'containerName' => $this->containerName]);
    }

    /**
     * Find Blobs by Tags operation finds all blobs in the storage account whose tags match a search expression.
     * @param array<string, scalar> $options
     * @return BlobTagQuery<BlobManager, Blobs>
     */
    public function findByTag(array $options = []): BlobTagQuery
    {
        /** @var BlobTagQuery<BlobManager, Blobs> */
        return azure_app(BlobTagQuery::class, ['manager' => $this])
            ->whenBuild(function (string $query) use ($options): Blobs {
                try {
                    $response = $this->request
                        ->withOptions($options)
                        ->get("{$this->containerName}/?restype=container&comp=blobs&where={$query}")
                        ->getBody();
                    // @codeCoverageIgnoreStart
                } catch (RequestExceptionInterface $e) {
                    throw RequestException::createFromRequestException($e);
                }
                // @codeCoverageIgnoreEnd

                /** @var array{Blobs?: array{Blob: BlobTypeStan|BlobTypeStan[]}} $parsed */
                $parsed = $this->request->getConfig()->parser->parse($response);

                return azure_app(Blobs::class, ['blobs' => $parsed['Blobs']['Blob'] ?? [], 'containerName' => $this->containerName]);
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

        return azure_app(File::class, ['name' => $blobName, 'content' => $content, 'options' => $headers]);
    }

    /** @param array<string, scalar> $options */
    public function putBlock(File $file, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::BLOB_TYPE         => BlobType::BLOCK->value,
                    Resource::BLOB_CONTENT_MD5  => $file->getContentMD5(),
                    Resource::BLOB_CONTENT_TYPE => $file->getContentType(),
                    Resource::CONTENT_MD5       => $file->getContentMD5(),
                    Resource::CONTENT_TYPE      => $file->getContentType(),
                    Resource::CONTENT_LENGTH    => $file->getContentLength(),
                ])
                ->put("{$this->containerName}/{$file->getFilename()}?resttype=blob", $file->getContent())
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
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param boolean $force If true, Delete the base blob and all of its snapshots.
     */
    public function delete(string $blobName, null|DateTime|string $snapshot = null, bool $force = false): bool
    {
        if ($snapshot instanceof DateTime) {
            $snapshot = convert_to_RFC3339_micro($snapshot);
        }

        $snapshotHeader = $snapshot ? sprintf('&snapshot=%s', urlencode($snapshot)) : '';

        $deleteSnapshotHeader = $snapshot ? sprintf('&%s=only', Resource::DELETE_SNAPSHOTS) : '';

        if ($force) {
            $deleteSnapshotHeader = sprintf('&%s=include', Resource::DELETE_SNAPSHOTS);
        }

        try {
            return $this->request
                ->delete("{$this->containerName}/{$blobName}?resttype=blob{$snapshotHeader}{$deleteSnapshotHeader}")
                ->isAccepted();
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function restore(string $blobName): bool
    {
        try {
            return $this->request
                ->put("{$this->containerName}/{$blobName}?comp=undelete&resttype=blob")
                ->isOk();
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function createSnapshot(string $blobName): bool
    {
        try {
            return $this->request
                ->put("{$this->containerName}/{$blobName}?comp=snapshot&resttype=blob")
                ->isCreated();
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function copy(string $sourceCopy, string $blobName, array $options = [], null|DateTime|string $snapshot = null): bool
    {
        if ($snapshot instanceof DateTime) {
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
            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function temporaryUrl(string $blobName, string|int|DateTimeInterface $expiresAt): string
    {
        /** @var DateTimeImmutable $expires */
        $expires = match(true) {
            $expiresAt instanceof DateTime => DateTimeImmutable::createFromMutable($expiresAt),
            is_int($expiresAt)             => DateTimeImmutable::createFromFormat('U', (string)$expiresAt),
            is_string($expiresAt)          => new DateTimeImmutable($expiresAt),
            default                        => $expiresAt,
        };

        if ($expires <= new DateTimeImmutable()) {
            throw InvalidArgumentException::create('Expiration time must be in the future');
        }

        $resource = "/{$this->containerName}/{$blobName}";

        $token = azure_app(UserDelegationSas::class, ['request' => $this->request->withResource($resource)])
            ->buildTokenUrl(AccessTokenPermission::READ, $expires);

        $uri = $this->request->uri("{$this->containerName}/{$blobName}");

        return $uri . $token;
    }

    public function lease(string $blobName): BlobLeaseManager
    {
        return azure_app(BlobLeaseManager::class, ['containerName' => $this->containerName, 'blobName' => $blobName]);
    }

    public function pages(): BlobPageManager
    {
        return azure_app(BlobPageManager::class, ['containerName' => $this->containerName])
            ->setManager($this);
    }

    public function properties(string $blobName): BlobPropertyManager
    {
        return azure_app(BlobPropertyManager::class, ['containerName' => $this->containerName, 'blobName' => $blobName]);
    }

    public function metadata(string $blobName): BlobMetadataManager
    {
        return azure_app(BlobMetadataManager::class, ['containerName' => $this->containerName, 'blobName' => $blobName]);
    }

    public function tags(string $blobName): BlobTagManager
    {
        return azure_app(BlobTagManager::class, ['containerName' => $this->containerName, 'blobName' => $blobName]);
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

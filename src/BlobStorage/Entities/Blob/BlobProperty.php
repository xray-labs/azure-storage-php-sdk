<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;

/**
 * @phpstan-type BlobPropertyHeaders array{Last-Modified?: string, x-ms-creation-time?: string, x-ms-tag-count?: int, x-ms-blob-type?: string, x-ms-copy-completion-time?: string|null, x-ms-copy-status-description?: string|null, x-ms-copy-id?: string|null, x-ms-copy-progress?: string|null, x-ms-copy-source?: string|null, x-ms-copy-status?: string|null, x-ms-incremental-copy?: string|null, x-ms-copy-destination-snapshot?: string|null, x-ms-lease-duration?: string|null, x-ms-lease-state?: string|null, x-ms-lease-status?: string|null, Content-Length?: int, Content-Type?: string|null, ETag?: string, Content-MD5?: string|null, Content-Encoding?: string|null, Content-Language?: string|null, Content-Disposition?: string|null, Cache-Control?: string|null, x-ms-blob-sequence-number?: int, x-ms-request-id?: string|null, x-ms-version?: string|null, Date?: string, Accept-Ranges?: string|null, x-ms-blob-committed-block-count?: string|null, x-ms-server-encrypted?: bool, x-ms-encryption-key-sha256?: string|null, x-ms-encryption-context?: string|null, x-ms-encryption-scope?: string|null, x-ms-access-tier?: string|null, x-ms-access-tier-inferred?: string|null, x-ms-archive-status?: string|null, x-ms-access-tier-change-time?: string|null, x-ms-client-request-id?: string|null, x-ms-rehydrate-priority?: string|null, x-ms-or-policy-id?: string|null, x-ms-last-access-time?: string|null, x-ms-blob-sealed?: string|null, x-ms-immutability-policy-until-date?: string|null, x-ms-immutability-policy-mode?: string|null, x-ms-legal-hold?: string|null, x-ms-owner?: string|null, x-ms-group?: string|null, x-ms-permissions?: string|null, x-ms-acl?: string|null, x-ms-resource-type?: string|null, x-ms-expiry-time?: string|null, leaseId?: string|null, sequenceNumberAction?: string|null, Origin?: string|null}
 * @suppressWarnings(PHPMD)
 */
final readonly class BlobProperty
{
    public DateTimeImmutable $lastModified;

    public DateTimeImmutable $creationTime;

    public BlobMetadata $metadata;

    public int $tagCount;

    public string $blobType;

    public ?DateTimeImmutable $copyCompletionTime;

    public ?string $copyStatusDescription;

    public ?string $copyId;

    public ?int $copyProgress;

    public ?string $copySource;

    public ?string $copyStatus; // FIX: Create Enum

    public ?bool $incrementalCopy;

    public ?DateTimeImmutable $copyDestinationSnapshot;

    public ?string $leaseDuration;

    public ?string $leaseState;

    public ?string $leaseStatus;

    public int $contentLength;

    public string $contentType;

    public string $eTag;

    public ?string $contentMD5;

    public ?string $contentEncoding;

    public ?string $contentLanguage;

    public ?string $contentDisposition;

    public ?string $cacheControl;

    public ?int $blobSequenceNumber;

    public ?string $requestId;

    public string $version;

    public DateTimeImmutable $date;

    public ?string $acceptRanges;

    public ?int $blobCommittedBlockCount;

    public bool $serverEncrypted;

    public ?string $encryptionKeySha256;

    public ?string $encryptionContext;

    public ?string $encryptionScope;

    public ?string $accessTier;

    public ?bool $accessTierInferred;

    public ?string $archiveStatus;

    public ?DateTimeImmutable $accessTierChangeTime;

    public ?string $clientRequestId;

    public ?string $rehydratePriority;

    public ?string $orPolicyId;

    public ?DateTimeImmutable $lastAccessTime;

    public ?bool $blobSealed;

    public ?DateTimeImmutable $immutabilityPolicyUntilDate;

    public ?string $immutabilityPolicyMode;

    public ?bool $legalHold;

    public ?string $owner;

    public ?string $group;

    public ?string $permissions;

    /** @var ?array<string> */
    public ?array $acl;

    public ?string $resourceType;

    public ?DateTimeImmutable $expiryTime;

    public ?string $leaseId;

    public ?string $sequenceNumberAction;

    public ?string $origin;

    /** @param BlobPropertyHeaders $property */
    public function __construct(array $property)
    {
        $this->lastModified                = new DateTimeImmutable($property['Last-Modified'] ?? 'now');
        $this->creationTime                = new DateTimeImmutable($property['x-ms-creation-time'] ?? 'now');
        $this->tagCount                    = (int) ($property['x-ms-tag-count'] ?? 0);
        $this->blobType                    = $property['x-ms-blob-type'] ?? '';
        $this->copyCompletionTime          = isset($property['x-ms-copy-completion-time']) ? new DateTimeImmutable($property['x-ms-copy-completion-time']) : null;
        $this->copyStatusDescription       = $property['x-ms-copy-status-description'] ?? null;
        $this->copyId                      = $property['x-ms-copy-id'] ?? null;
        $this->copyProgress                = isset($property['x-ms-copy-progress']) ? (int) $property['x-ms-copy-progress'] : null;
        $this->copySource                  = $property['x-ms-copy-source'] ?? null;
        $this->copyStatus                  = $property['x-ms-copy-status'] ?? null;
        $this->incrementalCopy             = to_boolean($property['x-ms-incremental-copy'] ?? false);
        $this->copyDestinationSnapshot     = isset($property['x-ms-copy-destination-snapshot']) ? new DateTimeImmutable($property['x-ms-copy-destination-snapshot']) : null;
        $this->leaseDuration               = $property['x-ms-lease-duration'] ?? null;
        $this->leaseState                  = $property['x-ms-lease-state'] ?? null;
        $this->leaseStatus                 = $property['x-ms-lease-status'] ?? null;
        $this->contentLength               = (int) ($property['Content-Length'] ?? 0);
        $this->contentType                 = $property['Content-Type'] ?? '';
        $this->eTag                        = $property['ETag'] ?? '';
        $this->contentMD5                  = $property['Content-MD5'] ?? null;
        $this->contentEncoding             = $property['Content-Encoding'] ?? null;
        $this->contentLanguage             = $property['Content-Language'] ?? null;
        $this->contentDisposition          = $property['Content-Disposition'] ?? null;
        $this->cacheControl                = $property['Cache-Control'] ?? null;
        $this->blobSequenceNumber          = (int) ($property['x-ms-blob-sequence-number'] ?? 0);
        $this->requestId                   = $property['x-ms-request-id'] ?? null;
        $this->version                     = $property['x-ms-version'] ?? '';
        $this->date                        = new DateTimeImmutable($property['Date'] ?? 'now');
        $this->acceptRanges                = $property['Accept-Ranges'] ?? null;
        $this->blobCommittedBlockCount     = isset($property['x-ms-blob-committed-block-count']) ? (int) $property['x-ms-blob-committed-block-count'] : null;
        $this->serverEncrypted             = to_boolean($property['x-ms-server-encrypted'] ?? false);
        $this->encryptionKeySha256         = $property['x-ms-encryption-key-sha256'] ?? null;
        $this->encryptionContext           = $property['x-ms-encryption-context'] ?? null;
        $this->encryptionScope             = $property['x-ms-encryption-scope'] ?? null;
        $this->accessTier                  = $property['x-ms-access-tier'] ?? null;
        $this->accessTierInferred          = to_boolean($property['x-ms-access-tier-inferred'] ?? false);
        $this->archiveStatus               = $property['x-ms-archive-status'] ?? null;
        $this->accessTierChangeTime        = isset($property['x-ms-access-tier-change-time']) ? new DateTimeImmutable($property['x-ms-access-tier-change-time']) : null;
        $this->clientRequestId             = $property['x-ms-client-request-id'] ?? null;
        $this->rehydratePriority           = $property['x-ms-rehydrate-priority'] ?? null;
        $this->orPolicyId                  = $property['x-ms-or-policy-id'] ?? null;
        $this->lastAccessTime              = isset($property['x-ms-last-access-time']) ? new DateTimeImmutable($property['x-ms-last-access-time']) : null;
        $this->blobSealed                  = to_boolean($property['x-ms-blob-sealed'] ?? false);
        $this->immutabilityPolicyUntilDate = isset($property['x-ms-immutability-policy-until-date']) ? new DateTimeImmutable($property['x-ms-immutability-policy-until-date']) : null;
        $this->immutabilityPolicyMode      = $property['x-ms-immutability-policy-mode'] ?? null;
        $this->legalHold                   = to_boolean($property['x-ms-legal-hold'] ?? false);
        $this->owner                       = $property['x-ms-owner'] ?? null;
        $this->group                       = $property['x-ms-group'] ?? null;
        $this->permissions                 = $property['x-ms-permissions'] ?? null;
        $this->resourceType                = $property['x-ms-resource-type'] ?? null;
        $this->expiryTime                  = isset($property['x-ms-expiry-time']) ? new DateTimeImmutable($property['x-ms-expiry-time']) : null;
        $this->acl                         = isset($property['x-ms-acl']) ? array_pad(explode(':', $property['x-ms-acl']), 4, '') : null;
        $this->metadata                    = new BlobMetadata(array_filter((array) $property, fn (string $key) => str_starts_with($key, Resource::METADATA_PREFIX), ARRAY_FILTER_USE_KEY));
        $this->leaseId                     = $property['leaseId'] ?? null;
        $this->sequenceNumberAction        = $property['sequenceNumberAction'] ?? null;
        $this->origin                      = $property['Origin'] ?? null;
    }

    /**
     * @return array{x-ms-blob-cache-control?: string, x-ms-blob-content-type?: string, x-ms-blob-content-md5?: string, x-ms-blob-content-encoding?: string, x-ms-blob-content-language?: string, x-ms-lease-id?: string, x-ms-client-request-id?: string, x-ms-blob-content-disposition?: string, Origin?: string, x-ms-blob-content-length?: int, x-ms-sequence-number-action?: string, x-ms-blob-sequence-number?: int}
     */
    public function getPropertiesToSave(): array
    {
        return array_filter([
            Resource::BLOB_CACHE_CONTROL       => $this->cacheControl,
            Resource::BLOB_CONTENT_TYPE        => $this->contentType,
            Resource::BLOB_CONTENT_MD5         => $this->contentMD5,
            Resource::BLOB_CONTENT_ENCODING    => $this->contentEncoding,
            Resource::BLOB_CONTENT_LANGUAGE    => $this->contentLanguage,
            Resource::LEASE_ID                 => $this->leaseId,
            Resource::CLIENT_REQUEST_ID        => $this->clientRequestId,
            Resource::BLOB_CONTENT_DISPOSITION => $this->contentDisposition,
            Resource::ORIGIN                   => $this->origin,
            Resource::BLOB_CONTENT_LENGTH      => $this->contentLength,
            Resource::SEQUENCE_NUMBER_ACTION   => $this->sequenceNumberAction,
            Resource::BLOB_SEQUENCE_NUMBER     => $this->blobSequenceNumber,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;

use function Xray\AzureStoragePhpSdk\Support\to_boolean;


/**
 * @phpstan-type PropertiesType array{Creation-Time?: string, Last-Modified?: string, Etag?: string, LeaseStatus?: string, LeaseState?: string, Owner?: string, Group?: string, Permissions?: string, Acl?: string, ResourceType?: string, Placeholder?: string, Content-Length?: string, Content-Type?: string, Content-Encoding?: string, Content-Language?: string, Content-MD5?: string, Cache-Control?: string, x-ms-blob-sequence-number?: string, BlobType?: string, AccessTier?: string, LeaseDuration?: string, CopyId?: string, CopyStatus?: string, CopySource?: string, CopyProgress?: string, CopyCompletionTime?: string, CopyStatusDescription?: string, ServerEncrypted?: string, CustomerProvidedKeySha256?: string, EncryptionContext?: string, EncryptionScope?: string, IncrementalCopy?: string, AccessTierInferred?: string, AccessTierChangeTime?: string, TagCount?: string, RehydratePriority?: string, ExpiryTime?: string, DeletedTime?: string, RemainingRetentionDays?: string}
 * @suppressWarnings(PHPMD.TooManyFields)
 * @suppressWarnings(PHPMD.CyclomaticComplexity)
 */
final readonly class Properties
{
    public DateTimeImmutable $creationTime;

    public DateTimeImmutable $lastModified;

    public string $eTag;

    public string $leaseStatus;

    public string $leaseState;

    public string $ownerUserId;

    public string $groupId;

    public string $permissions;

    public string $accessControlList;

    public string $resourceType;

    public string $placeholder;

    public string $contentLength;

    public string $contentType;

    public string $contentEncoding;

    public string $contentLanguage;

    public string $contentMD5;

    public string $cacheControl;

    public int $blobSequenceNumber;

    public string $blobType;

    public string $accessTier;

    public string $leaseDuration;

    public string $copyId;

    public string $copyStatus;

    public string $copySourceUrl;

    public string $copyProgress;

    public DateTimeImmutable $copyCompletionTime;

    public string $copyStatusDescription;

    public bool $serverEncrypted;

    public string $customerProvidedKeySha256;

    public string $encryptionContext;

    public string $encryptionScope;

    public bool $incrementalCopy;

    public bool $accessTierInferred;

    public DateTimeImmutable $accessTierChangeTime;

    public int $tagCount;

    public string $rehydratePriority;

    public DateTimeImmutable $expiryTime;

    public ?DateTimeImmutable $deletedTime;

    public ?int $remainingRetentionDays;

    /** @param PropertiesType $property */
    public function __construct(array $property)
    {
        $this->creationTime              = new DateTimeImmutable($property['Creation-Time'] ?? 'now');
        $this->lastModified              = new DateTimeImmutable($property['Last-Modified'] ?? 'now');
        $this->eTag                      = $property['Etag'] ?? '';
        $this->leaseStatus               = $property['LeaseStatus'] ?? '';
        $this->leaseState                = $property['LeaseState'] ?? '';
        $this->ownerUserId               = $property['Owner'] ?? '';
        $this->groupId                   = $property['Group'] ?? '';
        $this->permissions               = $property['Permissions'] ?? '';
        $this->accessControlList         = $property['Acl'] ?? '';
        $this->resourceType              = $property['ResourceType'] ?? '';
        $this->placeholder               = $property['Placeholder'] ?? '';
        $this->contentLength             = $property['Content-Length'] ?? '';
        $this->contentType               = isset($property['Content-Type']) && !empty($property['Content-Type']) ? $property['Content-Type'] : '';
        $this->contentEncoding           = isset($property['Content-Encoding']) && !empty($property['Content-Encoding']) ? $property['Content-Encoding'] : '';
        $this->contentLanguage           = isset($property['Content-Language']) && !empty($property['Content-Language']) ? $property['Content-Language'] : '';
        $this->contentMD5                = isset($property['Content-MD5']) && !empty($property['Content-MD5']) ? $property['Content-MD5'] : '';
        $this->cacheControl              = isset($property['Cache-Control']) && !empty($property['Cache-Control']) ? $property['Cache-Control'] : '';
        $this->blobSequenceNumber        = (int) ($property['x-ms-blob-sequence-number'] ?? 0);
        $this->blobType                  = $property['BlobType'] ?? '';
        $this->accessTier                = $property['AccessTier'] ?? '';
        $this->leaseDuration             = $property['LeaseDuration'] ?? '';
        $this->copyId                    = $property['CopyId'] ?? '';
        $this->copyStatus                = $property['CopyStatus'] ?? '';
        $this->copySourceUrl             = $property['CopySource'] ?? '';
        $this->copyProgress              = $property['CopyProgress'] ?? '';
        $this->copyCompletionTime        = new DateTimeImmutable($property['CopyCompletionTime'] ?? 'now');
        $this->copyStatusDescription     = $property['CopyStatusDescription'] ?? '';
        $this->serverEncrypted           = to_boolean($property['ServerEncrypted'] ?? false);
        $this->customerProvidedKeySha256 = $property['CustomerProvidedKeySha256'] ?? '';
        $this->encryptionContext         = $property['EncryptionContext'] ?? '';
        $this->encryptionScope           = $property['EncryptionScope'] ?? '';
        $this->incrementalCopy           = to_boolean($property['IncrementalCopy'] ?? false);
        $this->accessTierInferred        = to_boolean($property['AccessTierInferred'] ?? false);
        $this->accessTierChangeTime      = new DateTimeImmutable($property['AccessTierChangeTime'] ?? 'now');
        $this->tagCount                  = (int) ($property['TagCount'] ?? 0);
        $this->rehydratePriority         = $property['RehydratePriority'] ?? '';
        $this->expiryTime                = new DateTimeImmutable($property['ExpiryTime'] ?? 'now');
        $this->deletedTime               = isset($property['DeletedTime']) ? new DateTimeImmutable($property['DeletedTime']) : null;
        $this->remainingRetentionDays    = isset($property['RemainingRetentionDays']) ? (int) $property['RemainingRetentionDays'] : null;
    }
}

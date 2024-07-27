<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Enums;

enum BlobIncludeOption: string
{
    case SNAPSHOTS             = 'snapshots';
    case METADATA              = 'metadata';
    case UNCOMMITTED_BLOBS     = 'uncommittedblobs';
    case COPY                  = 'copy';
    case DELETED               = 'deleted';
    case TAGS                  = 'tags';
    case VERSIONS              = 'versions';
    case DELETED_WITH_VERSIONS = 'deletedwithversions';
    case IMMUTABILITY_POLICY   = 'immutabilitypolicy';
    case LEGAL_HOLD            = 'legalhold';
    case PERMISSIONS           = 'permissions';

    /** @return string[] */
    public static function toArray(): array
    {
        return array_map(fn (self $enum) => $enum->value, self::cases());
    }
}

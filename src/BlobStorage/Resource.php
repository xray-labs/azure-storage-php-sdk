<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

final class Resource
{
    public const string VERSION = '2024-05-04';

    public const string CANONICAL_HEADER_PREFIX = 'x-ms-';

    public const string AUTH_DATE    = 'x-ms-date';
    public const string AUTH_VERSION = 'x-ms-version';
    public const string AUTH_HEADER  = 'Authorization';

    public const string CLIENT_REQUEST_ID = 'x-ms-client-request-id';
    public const string ORIGIN            = 'Origin';
    public const string CONTENT_MD5       = 'Content-MD5';
    public const string CONTENT_TYPE      = 'Content-Type';
    public const string CONTENT_LENGTH    = 'Content-Length';
    public const string REQUEST_ID        = 'x-ms-request-id';

    public const string LEASE_ID = 'x-ms-lease-id';

    public const string LEASE_ACTION       = 'x-ms-lease-action';
    public const string LEASE_BREAK_PERIOD = 'x-ms-lease-break-period';
    public const string LEASE_DURATION     = 'x-ms-lease-duration';
    public const string LEASE_PROPOSED_ID  = 'x-ms-proposed-lease-id';
    public const string LEASE_STATUS       = 'x-ms-lease-status';
    public const string LEASE_STATE        = 'x-ms-lease-state';

    public const string DELETE_CONTAINER_NAME    = 'x-ms-deleted-container-name';
    public const string DELETE_CONTAINER_VERSION = 'x-ms-deleted-container-version';

    public const string METADATA_PREFIX = 'x-ms-meta-';

    public const string ACCESS_CONTROL_REQUEST_METHOD  = 'Access-Control-Request-Method';
    public const string ACCESS_CONTROL_REQUEST_HEADERS = 'Access-Control-Request-Headers';

    public const string PAGE_WRITE = 'x-ms-page-write';
    public const string RANGE      = 'x-ms-range';

    public const string BLOB_PUBLIC_ACCESS       = 'x-ms-blob-public-access';
    public const string BLOB_CACHE_CONTROL       = 'x-ms-blob-cache-control';
    public const string BLOB_CONTENT_TYPE        = 'x-ms-blob-content-type';
    public const string BLOB_CONTENT_MD5         = 'x-ms-blob-content-md5';
    public const string BLOB_CONTENT_ENCODING    = 'x-ms-blob-content-encoding';
    public const string BLOB_CONTENT_LANGUAGE    = 'x-ms-blob-content-language';
    public const string BLOB_CONTENT_DISPOSITION = 'x-ms-blob-content-disposition';
    public const string BLOB_CONTENT_LENGTH      = 'x-ms-blob-content-length';
    public const string BLOB_SEQUENCE_NUMBER     = 'x-ms-blob-sequence-number';
    public const string BLOB_TYPE                = 'x-ms-blob-type';

    public const string UNDELETE_SOURCE  = 'x-ms-undelete-source';
    public const string DELETE_SNAPSHOTS = 'x-ms-delete-snapshots';

    public const string EXPIRY_OPTION = 'x-ms-expiry-option';
    public const string EXPIRY_TIME   = 'x-ms-expiry-time';

    public const string COPY_SOURCE = 'x-ms-copy-source';

    public const string SEQUENCE_NUMBER_ACTION = 'x-ms-sequence-number-action';

    public static function canonicalize(string $uri): string
    {
        /** @var array<string, string> */
        $parsed = parse_url($uri);

        parse_str($parsed['query'] ?? '', $queryParams);

        ksort($queryParams);

        $result = '';

        /**
         * @var string $value
         * @var string $key
         */
        foreach ($queryParams as $key => $value) {
            $result .= mb_convert_case($key, MB_CASE_LOWER, 'UTF-8') . ':' . $value . "\n";
        }

        return $parsed['path'] . "\n" . rtrim($result, "\n");
    }
}

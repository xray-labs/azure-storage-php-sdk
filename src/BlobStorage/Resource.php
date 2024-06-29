<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

final class Resource
{
    public const string VERSION = '2024-05-04';

    public const string CANONICAL_HEADER_PREFIX = 'x-ms-';

    public const string AUTH_DATE_KEY    = 'x-ms-date';
    public const string AUTH_VERSION_KEY = 'x-ms-version';
    public const string AUTH_HEADER_KEY  = 'Authorization';

    public const string CLIENT_REQUEST_ID_KEY = 'x-ms-client-request-id';
    public const string ORIGIN_KEY            = 'Origin';
    public const string REQUEST_ID_KEY        = 'x-ms-request-id';

    public const string LEASE_ID_KEY           = 'x-ms-lease-id';
    public const string LEASE_ACTION_KEY       = 'x-ms-lease-action';
    public const string LEASE_BREAK_PERIOD_KEY = 'x-ms-lease-break-period';
    public const string LEASE_DURATION_KEY     = 'x-ms-lease-duration';
    public const string LEASE_PROPOSED_ID_KEY  = 'x-ms-proposed-lease-id';

    public const string DELETE_CONTAINER_NAME_KEY    = 'x-ms-deleted-container-name';
    public const string DELETE_CONTAINER_VERSION_KEY = 'x-ms-deleted-container-version';

    public const string CONTAINER_META_PREFIX = 'x-ms-meta-';

    public const string ACCESS_CONTROL_REQUEST_METHOD_KEY  = 'Access-Control-Request-Method';
    public const string ACCESS_CONTROL_REQUEST_HEADERS_KEY = 'Access-Control-Request-Headers';

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

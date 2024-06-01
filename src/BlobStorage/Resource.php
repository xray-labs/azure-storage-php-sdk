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

    public const string DELETE_CONTAINER_NAME_KEY    = 'x-ms-deleted-container-name';
    public const string DELETE_CONTAINER_VERSION_KEY = 'x-ms-deleted-container-version';

    public const string CONTAINER_META_PREFIX = 'x-ms-meta-';

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

        return ($parsed['path'] ?: '') . "\n" . rtrim($result, "\n");
    }
}

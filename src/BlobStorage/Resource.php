<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

final class Resource
{
    public const string VERSION = '2024-05-04';

    public const string AUTH_DATE_KEY    = 'x-ms-date';
    public const string AUTH_VERSION_KEY = 'x-ms-version';
    public const string AUTH_HEADER_KEY  = 'Authorization';
}

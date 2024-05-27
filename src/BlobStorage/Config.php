<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

final readonly class Config
{
    public function __construct(
        public string $account,
        public string $key,
        public string $version,
    ) {
        //
    }
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;

interface HasConfig
{
    public function getConfig(): Config;
}

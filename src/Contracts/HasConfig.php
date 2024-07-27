<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts;

use Xray\AzureStoragePhpSdk\BlobStorage\Config;

interface HasConfig
{
    public function getConfig(): Config;
}

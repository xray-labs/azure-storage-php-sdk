<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;

interface HasRequestShared
{
    public function getRequest(): Request;
}

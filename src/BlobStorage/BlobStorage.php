<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Sjpereira\AzureStoragePhpSdk\Http\Request;

final class BlobStorage
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function account(): AccountManager
    {
        return new AccountManager($this->request);
    }

    public function containers(): ContainerManager
    {
        return new ContainerManager($this->request);
    }
}

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;

final class BlobStorage
{
    public function __construct(protected RequestContract $request)
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

    public function blobs(string $containerName): BlobManager
    {
        return new BlobManager($this->request, $containerName);
    }
}

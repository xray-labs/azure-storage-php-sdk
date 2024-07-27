<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Xray\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;

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

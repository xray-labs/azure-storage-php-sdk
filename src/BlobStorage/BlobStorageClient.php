<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;
use Xray\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Xray\AzureStoragePhpSdk\Http\Request;

final class BlobStorageClient
{
    public function __construct(protected RequestContract $request)
    {
        //
    }

    /** @param array{version?: string, parser?: Parser, converter?: Converter} $config */
    public static function create(Auth $auth, array $config = []): static
    {
        return new static(new Request($auth, new Config($config)));
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

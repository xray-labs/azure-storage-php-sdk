<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\HasFakeRequest;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;
use Xray\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Xray\AzureStoragePhpSdk\Http\Request;

class BlobStorageClient
{
    use HasFakeRequest;

    public function __construct(protected RequestContract $request)
    {
        azure_app()->instance(RequestContract::class, $this->request);
        azure_app()->instance(Config::class, $this->request->getConfig());
    }

    /** @param array{version?: string, parser?: Parser, converter?: Converter} $config */
    public static function create(Auth $auth, array $config = []): static
    {
        /** @phpstan-ignore-next-line */
        return new static(new Request($auth, new Config($config)));
    }

    public function getRequest(): RequestContract
    {
        return $this->request;
    }

    public function getConfig(): Config
    {
        return $this->request->getConfig();
    }

    public function account(): AccountManager
    {
        return azure_app(AccountManager::class);
    }

    public function containers(): ContainerManager
    {
        return azure_app(ContainerManager::class);
    }

    public function blobs(string $containerName): BlobManager
    {
        return azure_app(BlobManager::class, ['containerName' => $containerName]);
    }
}

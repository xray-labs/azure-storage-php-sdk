<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Sjpereira\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Sjpereira\AzureStoragePhpSdk\Http\Request;

final class BlobStorage
{
    public function __construct(protected RequestContract $request)
    {
        //
    }

    /** @param array{account: string, key: string, version?: string, parser?: Parser, converter?: Converter, auth?: Auth} $options */
    public static function client(array $options, ?RequestContract $request = null): self
    {
        $config = new Config($options);

        return new self($request ?? new Request($config));
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

<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use GuzzleHttp\Client;
use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
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

        return new self($request ?? new Request(new Client(), $config));
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

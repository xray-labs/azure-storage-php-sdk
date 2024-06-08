<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use GuzzleHttp\Client;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

/**
 * @phpstan-import-type ConfigType from Config
 */
final class BlobStorage
{
    public function __construct(protected RequestContract $request)
    {
        //
    }

    /** @param ConfigType $options */
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

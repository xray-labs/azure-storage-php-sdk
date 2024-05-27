<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

final class BlobStorage
{
    public function __construct(
        protected Config $config,
        protected Request $request,
    ) {
        //
    }

    public function account(): Account
    {
        return new Account($this->config, $this->request);
    }

    public function containers(): ContainerManager
    {
        return new ContainerManager($this->config, $this->request);
    }
}

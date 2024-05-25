<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account;
use Sjpereira\AzureStoragePhpSdk\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Parsers\Contracts\Parser;

class BlobStorage
{
    public function __construct(
        protected Config $config,
        protected Request $request,
        protected Parser $parser,
    ) {
        //
    }

    public function account(): Account
    {
        return new Account($this->request, $this->parser);
    }
}

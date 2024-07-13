<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;

/** @template TRequest of Request */
interface RequestShared
{
    /** @return TRequest */
    public function getRequest(): Request;
}

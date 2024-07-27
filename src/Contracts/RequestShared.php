<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts;

use Xray\AzureStoragePhpSdk\Contracts\Http\Request;

/** @template TRequest of Request */
interface RequestShared
{
    /** @return TRequest */
    public function getRequest(): Request;
}

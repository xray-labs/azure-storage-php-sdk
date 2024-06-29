<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Concerns;

use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;

/** @template TRequest of Request */
trait HasRequestShared
{
    /** @return TRequest */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
